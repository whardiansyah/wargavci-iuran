<?php

namespace App\Http\Controllers;

use App\Models\MasterConfig;
use App\Models\MasterPenghuni;
use App\Models\Pembayaran;
use App\Models\PembayaranSisa;
use App\Models\PencatatanAir;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Tagihan::class);

        $periode = $request->query->has('periode')
            ? $request->get('periode')
            : now()->format('Y-m');
        if (!$periode || !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode)) {
            $periode = now()->format('Y-m');
        }
        $nomorRumah = $request->get('nomor_rumah');

        $query = Tagihan::with('masterPenghuni')
            ->orderBy('periode', 'desc')
            // ->orderBy('code')
            ->orderBy(
                MasterPenghuni::select('nomor_rumah')
                    ->whereColumn('master_penghuni.id', 'tagihan.master_penghuni_id')
                    ->limit(1)
            );

        if ($periode && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode)) {
            $query->where('periode', $periode);
        }

        if ($nomorRumah) {
            $query->whereHas('masterPenghuni', function ($q) use ($nomorRumah) {
                $q->where('nomor_rumah', $nomorRumah);
            });
        }

        $tagihan = $query->paginate(10)->withQueryString();
        $masterPenghunis = MasterPenghuni::orderBy('nomor_rumah')->get();
        $iuranConfigs = $this->iuranConfigs();

        return view('tagihan.index', compact('tagihan', 'periode', 'nomorRumah', 'masterPenghunis', 'iuranConfigs'));
    }

    public function create()
    {
        $this->authorize('create', Tagihan::class);

        return view('tagihan.create', [
            'tagihan' => new Tagihan(['periode' => now()->format('Y-m'), 'status_bayar' => 'belum']),
            'masterPenghunis' => MasterPenghuni::orderBy('nomor_rumah')->get(),
            'iuranConfigs' => $this->iuranConfigs(),
        ]);
    }

    public function rutin(Request $request)
    {
        $this->authorize('viewAny', Tagihan::class);

        $periode = $request->query->has('periode')
            ? $request->get('periode')
            : now()->format('Y-m');
        if (!$periode || !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode)) {
            $periode = now()->format('Y-m');
        }
        $nomorRumah = $request->get('nomor_rumah');
        $statusBayar = $request->get('status_bayar');
        if (!in_array($statusBayar, ['belum', 'sudah'], true)) {
            $statusBayar = null;
        }

        $query = Tagihan::with('masterPenghuni')
            ->orderBy(
                MasterPenghuni::select('nomor_rumah')
                    ->whereColumn('master_penghuni.id', 'tagihan.master_penghuni_id')
                    ->limit(1)
            )
            ->orderBy('code');

        if ($periode && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode)) {
            $query->where('periode', $periode);
        }

        if ($nomorRumah) {
            $query->whereHas('masterPenghuni', function ($q) use ($nomorRumah) {
                $q->where('nomor_rumah', $nomorRumah);
            });
        }

        if ($statusBayar) {
            $query->where('status_bayar', $statusBayar);
        }

        $items = $query->get();
        $codes = $items->pluck('code')->unique()->sort()->values();
        $penghuniIds = $items->pluck('master_penghuni_id')->unique();
        [$year, $month] = explode('-', $periode);
        $pencatatanAirs = PencatatanAir::whereIn('master_penghuni_id', $penghuniIds)
            ->where('periode_tahun', (int) $year)
            ->where('periode_bulan', (int) $month)
            ->get()
            ->keyBy('master_penghuni_id');
        $pembayarans = Pembayaran::whereIn('master_penghuni_id', $penghuniIds)
            ->where('periode', $periode)
            ->get()
            ->keyBy('master_penghuni_id');
        $pembayaranSisas = PembayaranSisa::whereIn('master_penghuni_id', $penghuniIds)
            ->orderByDesc('periode')
            ->get()
            ->unique('master_penghuni_id')
            ->keyBy('master_penghuni_id');
        $rows = $items->groupBy('master_penghuni_id')->map(function ($group) use ($codes, $pencatatanAirs, $pembayarans, $pembayaranSisas) {
            $values = [];
            foreach ($codes as $code) {
                $values[$code] = (int) ($group->firstWhere('code', $code)?->nilai ?? 0);
            }
            $total = array_sum($values);
            $sisaBayar = (int) ($pembayaranSisas->get($group->first()->master_penghuni_id)?->sisa_lebih_bayar ?? 0);
            $sisaBayarkalian = $sisaBayar * -1;

            // if($total <= $sisaBayar)
            // {
            //     $sisaBayarkalian = $total * -1;
            // } 
            $totalTagihan = $total + $sisaBayarkalian;

            return [
                'masterPenghuni' => $group->first()->masterPenghuni,
                'values' => $values,
                'total' => $total,
                'sisaBayar' => $sisaBayar,
                'totalTagihan' => $totalTagihan,
                'statusBayar' => $group->every(fn ($item) => $item->status_bayar === 'sudah') ? 'sudah' : 'belum',
                'pencatatanAir' => $pencatatanAirs->get($group->first()->master_penghuni_id),
                'pembayaran' => $pembayarans->get($group->first()->master_penghuni_id) ?? new Pembayaran([
                    'jumlah_tagihan' => $totalTagihan,
                    'jumlah_bayar' => 0,
                    'sisa_lebih_bayar' => 0 - $totalTagihan,
                    'tanggal_bayar' => now()->toDateString(),
                ]),
            ];
        })->values();
        $columnTotals = [];
        foreach ($codes as $code) {
            $columnTotals[$code] = $rows->sum(fn ($row) => $row['values'][$code] ?? 0);
        }
        $grandTotal = array_sum($columnTotals);
        $masterPenghunis = MasterPenghuni::orderBy('nomor_rumah')->get();
        $monthNames = [
            1 => 'JANUARI',
            2 => 'FEBRUARI',
            3 => 'MARET',
            4 => 'APRIL',
            5 => 'MEI',
            6 => 'JUNI',
            7 => 'JULI',
            8 => 'AGUSTUS',
            9 => 'SEPTEMBER',
            10 => 'OKTOBER',
            11 => 'NOVEMBER',
            12 => 'DESEMBER',
        ];
        $periodeBulan = $monthNames[(int) $month] ?? '';
        $periodeTahun = (int) $year;

        return view('tagihan.rutin', compact('rows', 'codes', 'columnTotals', 'grandTotal', 'periode', 'periodeBulan', 'periodeTahun', 'nomorRumah', 'statusBayar', 'masterPenghunis'));
    }

    public function simpanPembayaran(Request $request)
    {
        $this->authorize('create', Tagihan::class);

        $validated = $request->validate([
            'master_penghuni_id' => ['required', 'exists:master_penghuni,id'],
            'periode' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'jumlah_tagihan' => ['required', 'integer', 'min:0'],
            'jumlah_bayar' => ['required', 'integer', 'min:0'],
            'tanggal_bayar' => ['nullable', 'date'],
            'cara_bayar' => ['nullable', Rule::in(['Cash', 'LinkAja', 'Jago', 'BSI', 'Mandiri'])],
            'nomor_rumah' => ['nullable', 'string'],
            'status_bayar' => ['nullable', Rule::in(['belum', 'sudah'])],
        ], [
            'master_penghuni_id.required' => 'Rumah harus dipilih.',
            'periode.required' => 'Periode harus diisi.',
            'jumlah_tagihan.required' => 'Jumlah tagihan harus diisi.',
            'jumlah_bayar.required' => 'Jumlah bayar harus diisi.',
        ]);

        // $validated['jumlah_tagihan'] = (int) Tagihan::where('master_penghuni_id', $validated['master_penghuni_id'])
        //     ->where('periode', $validated['periode'])
        //     ->sum('nilai');
        $validated['sisa_lebih_bayar'] = (int) $validated['jumlah_bayar'] - (int) $validated['jumlah_tagihan'];
        $nomorRumah = $validated['nomor_rumah'] ?? null;
        $statusBayar = $validated['status_bayar'] ?? null;
        unset($validated['nomor_rumah']);
        unset($validated['status_bayar']);

        Pembayaran::updateOrCreate(
            [
                'master_penghuni_id' => $validated['master_penghuni_id'],
                'periode' => $validated['periode'],
            ],
            $validated
        );

        if ($validated['sisa_lebih_bayar'] !== 0) {
            PembayaranSisa::updateOrCreate(
                [
                    'master_penghuni_id' => $validated['master_penghuni_id'],
                    'periode' => $validated['periode'],
                ],
                ['sisa_lebih_bayar' => $validated['sisa_lebih_bayar']]
            );
        } else {
            PembayaranSisa::where('master_penghuni_id', $validated['master_penghuni_id'])
                ->delete();
        }

        Tagihan::where('master_penghuni_id', $validated['master_penghuni_id'])
            ->where('periode', $validated['periode'])
            ->update(['status_bayar' => 'sudah']);

        return redirect()->route('tagihan.rutin', [
            'periode' => $validated['periode'],
            'nomor_rumah' => $nomorRumah,
            'status_bayar' => $statusBayar,
        ])->with('success', 'Pembayaran berhasil disimpan.');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Tagihan::class);

        Tagihan::create($this->validatedData($request));

        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil ditambahkan.');
    }

    public function show(Tagihan $tagihan)
    {
        $this->authorize('view', $tagihan);

        return view('tagihan.show', compact('tagihan'));
    }

    public function edit(Tagihan $tagihan)
    {
        $this->authorize('update', $tagihan);

        return view('tagihan.edit', [
            'tagihan' => $tagihan,
            'masterPenghunis' => MasterPenghuni::orderBy('nomor_rumah')->get(),
            'iuranConfigs' => $this->iuranConfigs(),
        ]);
    }

    public function update(Request $request, Tagihan $tagihan)
    {
        $this->authorize('update', $tagihan);

        $tagihan->update($this->validatedData($request, $tagihan));

        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Tagihan $tagihan)
    {
        $this->authorize('delete', $tagihan);

        $tagihan->delete();

        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dihapus.');
    }

    public function generate(Request $request)
    {
        $this->authorize('create', Tagihan::class);

        $validated = $request->validate([
            'periode' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        ], [
            'periode.required' => 'Periode harus diisi.',
            'periode.regex' => 'Format periode harus YYYY-MM.',
        ]);

        $hasSudah = Tagihan::where('periode', $validated['periode'])
            ->where('status_bayar', 'sudah')
            ->exists();

        if ($hasSudah) {
            return redirect()->route('tagihan.index', ['periode' => $validated['periode']])
                ->with('error_sudah', "Data tidak bisa di-generate ulang karena terdapat tagihan yang sudah dibayar pada periode {$validated['periode']}.");
        }

        $configs = $this->iuranConfigs();
        if ($configs->isEmpty()) {
            return redirect()->route('tagihan.index', ['periode' => $validated['periode']])
                ->with('error', 'Tidak ada master config dengan tipe iuran.');
        }

        $penghunis = MasterPenghuni::where('status', 'aktif')->orderBy('nomor_rumah')->get();
        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($configs, $penghunis, $validated, &$created, &$skipped) {
            foreach ($penghunis as $penghuni) {
                foreach ($configs as $config) {
                    if ($config->code === 'iuran-kurleb') {
                        $pembayaranSisa = PembayaranSisa::where('master_penghuni_id', $penghuni->id)
                            ->where('periode', $validated['periode'])
                            ->first();

                        if (!$pembayaranSisa) {
                            $skipped++;
                            continue;
                        }
                    }

                    $tagihan = Tagihan::firstOrCreate(
                        [
                            'master_penghuni_id' => $penghuni->id,
                            'periode' => $validated['periode'],
                            'code' => $config->code,
                        ],
                        [
                            'nilai' => $this->nilaiTagihan($config, $penghuni, $validated['periode']),
                            'status_bayar' => 'belum',
                        ]
                    );

                    $tagihan->wasRecentlyCreated ? $created++ : $skipped++;
                }
            }
        });

        return redirect()->route('tagihan.index', ['periode' => $validated['periode']])
            ->with('success', "Generate selesai. {$created} tagihan dibuat, {$skipped} sudah ada.");
    }

    public function reset(Request $request)
    {
        $this->authorize('reset', Tagihan::class);

        $validated = $request->validate([
            'periode' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        ], [
            'periode.required' => 'Periode harus diisi.',
            'periode.regex' => 'Format periode harus YYYY-MM.',
        ]);

        $hasSudah = Tagihan::where('periode', $validated['periode'])
            ->where('status_bayar', 'sudah')
            ->exists();

        if ($hasSudah) {
            return redirect()->route('tagihan.index', ['periode' => $validated['periode']])
                ->with('error_sudah', "Data tidak bisa di-reset karena terdapat tagihan yang sudah dibayar pada periode {$validated['periode']}.");
        }

        $deleted = Tagihan::where('periode', $validated['periode'])->delete();

        return redirect()->route('tagihan.index', ['periode' => $validated['periode']])
            ->with('success', "Reset selesai. {$deleted} tagihan periode {$validated['periode']} dihapus.");
    }

    private function validatedData(Request $request, ?Tagihan $tagihan = null): array
    {
        $ignoreId = $tagihan?->id;

        return $request->validate([
            'master_penghuni_id' => ['required', 'exists:master_penghuni,id'],
            'periode' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tagihan')
                    ->where(fn ($query) => $query
                        ->where('master_penghuni_id', $request->get('master_penghuni_id'))
                        ->where('periode', $request->get('periode')))
                    ->ignore($ignoreId),
            ],
            'nilai' => ['required', 'integer', 'min:0'],
            'status_bayar' => ['required', Rule::in(['belum', 'sudah'])],
        ], [
            'master_penghuni_id.required' => 'Rumah harus dipilih.',
            'master_penghuni_id.exists' => 'Rumah tidak valid.',
            'periode.required' => 'Periode harus diisi.',
            'periode.regex' => 'Format periode harus YYYY-MM.',
            'code.required' => 'Kode tagihan harus diisi.',
            'code.unique' => 'Tagihan dengan kode ini sudah ada untuk rumah dan periode tersebut.',
            'nilai.required' => 'Nilai tagihan harus diisi.',
            'status_bayar.required' => 'Status bayar harus dipilih.',
        ]);
    }

    private function iuranConfigs()
    {
        return MasterConfig::where('type', 'iuran')->orderBy('code')->get();
    }

    private function nilaiTagihan(MasterConfig $config, MasterPenghuni $penghuni, string $periode): int
    {
        if ($config->code === 'iuran-kurleb') {
            return (float) (PembayaranSisa::where('master_penghuni_id', $penghuni->id)
                ->value('sisa_lebih_bayar') ?? 0);
        }

        if ($config->code === 'iuran-air') {
            [$year, $month] = explode('-', $periode);

            return (int) (PencatatanAir::where('master_penghuni_id', $penghuni->id)
                ->where('periode_tahun', (int) $year)
                ->where('periode_bulan', (int) $month)
                ->value('total_tagihan') ?? 0);
        }

        return (int) $config->value;
    }
}
