<?php

namespace App\Http\Controllers;

use App\Models\TransaksiKas;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TransaksiKasController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', TransaksiKas::class);

        $tanggalMulai = $request->query->has('tanggal_mulai')
            ? $request->get('tanggal_mulai')
            : now()->startOfMonth()->toDateString();
        $tanggalSelesai = $request->query->has('tanggal_selesai')
            ? $request->get('tanggal_selesai')
            : now()->endOfMonth()->toDateString();
        $deskripsi = $request->get('deskripsi');

        $query = TransaksiKas::query()
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'asc');

        $this->applyDateRangeFilter($query, $tanggalMulai, $tanggalSelesai);
        $this->applyDeskripsiFilter($query, $deskripsi);

        $items = $this->withRunningBalance($query->get());
        $transaksiKas = $this->paginateCollection($items, $request);

        return view('transaksi_kas.index', compact('transaksiKas', 'tanggalMulai', 'tanggalSelesai', 'deskripsi'));
    }

    public function create()
    {
        $this->authorize('create', TransaksiKas::class);

        return view('transaksi_kas.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', TransaksiKas::class);

        TransaksiKas::create($this->validatedData($request));

        return redirect()->route('transaksi_kas.index')->with('success', 'Transaksi kas berhasil ditambahkan');
    }

    public function show(TransaksiKas $transaksiKas)
    {
        $this->authorize('view', $transaksiKas);

        return view('transaksi_kas.show', compact('transaksiKas'));
    }

    public function edit(TransaksiKas $transaksiKas)
    {
        $this->authorize('update', $transaksiKas);

        return view('transaksi_kas.edit', compact('transaksiKas'));
    }

    public function update(Request $request, TransaksiKas $transaksiKas)
    {
        $this->authorize('update', $transaksiKas);

        $transaksiKas->update($this->validatedData($request));

        return redirect()->route('transaksi_kas.index')->with('success', 'Transaksi kas berhasil diperbarui');
    }

    public function destroy(TransaksiKas $transaksiKas)
    {
        $this->authorize('delete', $transaksiKas);

        $transaksiKas->delete();

        return redirect()->route('transaksi_kas.index')->with('success', 'Transaksi kas berhasil dihapus');
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('export', TransaksiKas::class);

        $tanggalMulai = $request->query->has('tanggal_mulai')
            ? $request->get('tanggal_mulai')
            : now()->startOfMonth()->toDateString();
        $tanggalSelesai = $request->query->has('tanggal_selesai')
            ? $request->get('tanggal_selesai')
            : now()->endOfMonth()->toDateString();
        $deskripsi = $request->get('deskripsi');

        $query = TransaksiKas::query()
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');

        $this->applyDateRangeFilter($query, $tanggalMulai, $tanggalSelesai);
        $this->applyDeskripsiFilter($query, $deskripsi);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Transaksi Kas');

        $headers = [
            'A1' => 'ID',
            'B1' => 'Tanggal',
            'C1' => 'Kode',
            'D1' => 'Deskripsi',
            'E1' => 'Keterangan',
            'F1' => 'Kredit',
            'G1' => 'Debet',
            'H1' => 'Nomor Ref',
            'I1' => 'Saldo',
            'J1' => 'Jenis',
            'K1' => 'Periode Bulan',
            'L1' => 'Periode Tahun',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $row = 2;
        foreach ($this->withRunningBalance($query->get()) as $item) {
            $sheet->setCellValue('A' . $row, $item->id);
            $sheet->setCellValue('B' . $row, $item->tanggal?->format('Y-m-d'));
            $sheet->setCellValue('C' . $row, $item->kode);
            $sheet->setCellValue('D' . $row, $item->deskripsi);
            $sheet->setCellValue('E' . $row, $item->keterangan);
            $sheet->setCellValue('F' . $row, $item->kredit);
            $sheet->setCellValue('G' . $row, $item->debet);
            $sheet->setCellValue('H' . $row, $item->nomor_ref);
            $sheet->setCellValue('I' . $row, $item->saldo_berjalan);
            $sheet->setCellValue('J' . $row, $item->jenis);
            $sheet->setCellValue('K' . $row, $item->periode_bulan);
            $sheet->setCellValue('L' . $row, $item->periode_tahun);
            $row++;
        }

        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'transaksi_kas_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'tanggal' => 'nullable|date',
            'kode' => 'nullable|string|max:20',
            'deskripsi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'kredit' => 'required|integer|min:0',
            'debet' => 'required|integer|min:0',
            'nomor_ref' => 'nullable|string|max:50',
            'saldo' => 'required|integer|min:0',
            'jenis' => ['required', Rule::in(['saldo_awal', 'transaksi'])],
            'periode' => ['nullable', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        ], [
            'deskripsi.required' => 'Deskripsi harus diisi',
            'kredit.required' => 'Kredit harus diisi',
            'debet.required' => 'Debet harus diisi',
            'saldo.required' => 'Saldo harus diisi',
            'periode.regex' => 'Format periode harus YYYY-MM',
        ]);

        $periode = $validated['periode'] ?? null;
        unset($validated['periode']);

        $validated['periode_bulan'] = null;
        $validated['periode_tahun'] = null;

        if ($periode) {
            [$year, $month] = explode('-', $periode);
            $validated['periode_bulan'] = (int) $month;
            $validated['periode_tahun'] = (int) $year;
        }

        return $validated;
    }

    private function applyDateRangeFilter($query, ?string $tanggalMulai, ?string $tanggalSelesai): void
    {
        if ($tanggalMulai && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalMulai)) {
            $query->whereDate('tanggal', '>=', $tanggalMulai);
        }

        if ($tanggalSelesai && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalSelesai)) {
            $query->whereDate('tanggal', '<=', $tanggalSelesai);
        }
    }

    private function applyDeskripsiFilter($query, ?string $deskripsi): void
    {
        if ($deskripsi !== null && trim($deskripsi) !== '') {
            $query->where('deskripsi', 'like', '%' . trim($deskripsi) . '%');
        }
    }

    private function withRunningBalance($items)
    {
        $saldo = 0;

        return $items->map(function ($item) use (&$saldo) {
            $saldo += (int) $item->kredit - (int) $item->debet;
            $item->saldo_berjalan = $saldo;

            return $item;
        });
    }

    private function paginateCollection($items, Request $request): LengthAwarePaginator
    {
        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
