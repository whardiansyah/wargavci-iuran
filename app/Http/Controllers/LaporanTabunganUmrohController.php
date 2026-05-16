<?php

namespace App\Http\Controllers;

use App\Models\TabunganUmroh;
use Illuminate\Http\Request;

class LaporanTabunganUmrohController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', TabunganUmroh::class);

        $chartData = TabunganUmroh::query()
            ->with('anggota:id,nama')
            ->selectRaw('anggota_id, SUM(nominal) as total_nominal')
            ->groupBy('anggota_id')
            ->orderByDesc('total_nominal')
            ->get()
            ->map(fn ($row) => [
                'anggota_id' => $row->anggota_id,
                'nama' => $row->anggota->nama ?? '-',
                'total_nominal' => (int) $row->total_nominal,
            ]);
        
        $kasBulanBerjalan = TabunganUmroh::query()
            ->selectRaw('SUM(nominal) as total_nominal')
            ->first();

        return view('laporan.tabungan_umroh', compact('chartData', 'kasBulanBerjalan'));
    }

    public function detail(Request $request, int $anggotaId)
    {
        $this->authorize('viewAny', TabunganUmroh::class);

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
