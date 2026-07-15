<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\TabunganUmroh;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanTabunganUmrohController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewReport', TabunganUmroh::class);

        $programId = $request->input('program_id');

        $baseQuery = TabunganUmroh::query()
            ->when($programId, fn ($q) => $q->whereHas(
                'anggota', fn ($q2) => $q2->where('program_id', $programId)
            ));

        $chartData = (clone $baseQuery)
            ->with('anggota:id,nama,program_id')
            ->selectRaw('anggota_id, SUM(nominal) as total_nominal')
            ->groupBy('anggota_id')
            ->orderByDesc('total_nominal')
            ->get()
            ->map(fn ($row) => [
                'anggota_id' => $row->anggota_id,
                'nama' => $row->anggota->nama ?? '-',
                'total_nominal' => (int) $row->total_nominal,
            ]);

        $totalSetoran = (clone $baseQuery)->sum('nominal');
        $jumlahTransaksi = (clone $baseQuery)->count();
        $jumlahAnggota = (clone $baseQuery)->distinct('anggota_id')->count('anggota_id');
        $rataRataPerAnggota = $jumlahAnggota > 0 ? (int) round($totalSetoran / $jumlahAnggota) : 0;

        $trendData = (clone $baseQuery)
            ->selectRaw('DATE_FORMAT(tanggal, "%Y-%m") as month_key, SUM(nominal) as total_nominal')
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->map(fn ($row) => [
                'label' => Carbon::createFromFormat('Y-m', $row->month_key)->translatedFormat('M Y'),
                'total_nominal' => (int) $row->total_nominal,
            ]);

        $topAnggota = (clone $baseQuery)
            ->with('anggota:id,nama')
            ->selectRaw('anggota_id, SUM(nominal) as total_nominal')
            ->groupBy('anggota_id')
            ->orderByDesc('total_nominal')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'anggota_id' => $row->anggota_id,
                'nama' => $row->anggota->nama ?? '-',
                'total_nominal' => (int) $row->total_nominal,
            ]);

        $metodeSetor = (clone $baseQuery)
            ->selectRaw('cara_setor, SUM(nominal) as total_nominal')
            ->groupBy('cara_setor')
            ->orderByDesc('total_nominal')
            ->get();

        $recentTransactions = (clone $baseQuery)
            ->with('anggota:id,nama')
            ->orderByDesc('tanggal')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'tanggal' => $row->tanggal?->format('d/m/Y') ?? '-',
                'nama' => $row->anggota->nama ?? '-',
                'nominal' => (int) $row->nominal,
                'cara_setor' => $row->cara_setor ?? '-',
                'keterangan' => $row->keterangan ?? '-',
            ]);

        $kasBulanBerjalan = (clone $baseQuery)
            ->selectRaw('SUM(nominal) as total_nominal')
            ->first();

        $programs = Program::orderBy('nama')->get(['id', 'nama']);
        $programLabel = $programId ? ($programs->firstWhere('id', $programId)?->nama ?? 'Program dipilih') : 'Semua Program';

        return view('laporan.tabungan_umroh', compact(
            'chartData',
            'kasBulanBerjalan',
            'programs',
            'programId',
            'programLabel',
            'totalSetoran',
            'jumlahAnggota',
            'jumlahTransaksi',
            'rataRataPerAnggota',
            'topAnggota',
            'metodeSetor',
            'trendData',
            'recentTransactions'
        ));
    }

    public function detail(Request $request, int $anggotaId)
    {
        $this->authorize('viewReport', TabunganUmroh::class);

        $data = TabunganUmroh::query()
            ->with('anggota:id,nama')
            ->where('anggota_id', $anggotaId)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'nama' => $data->first()?->anggota->nama ?? '-',
            'total' => $data->sum('nominal'),
            'rows' => $data->map(fn ($row) => [
                'tanggal' => $row->tanggal->format('d/m/Y'),
                'nominal' => $row->nominal,
                'cara_setor' => $row->cara_setor,
                'keterangan' => $row->keterangan ?? '-',
            ]),
        ]);
    }
}
