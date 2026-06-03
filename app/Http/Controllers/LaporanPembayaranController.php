<?php

namespace App\Http\Controllers;

use App\Models\MasterPenghuni;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class LaporanPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Tagihan::class);

        $periode = $request->query->has('periode')
            ? $request->get('periode')
            : now()->format('Y-m');
        $nomorRumah = $request->get('nomor_rumah');

        $query = Pembayaran::query()
            ->with('masterPenghuni:id,nomor_rumah,kepala_keluarga')
            ->selectRaw('master_penghuni_id, periode, SUM(jumlah_bayar) as total_bayar')
            ->groupBy('master_penghuni_id', 'periode');

        if ($periode && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode)) {
            $query->where('periode', $periode);
        }

        if ($nomorRumah) {
            $query->whereHas('masterPenghuni', function ($q) use ($nomorRumah) {
                $q->where('nomor_rumah', $nomorRumah);
            });
        }

        $rows = $query
            ->orderBy('periode')
            ->orderByDesc('total_bayar')
            ->get();

        $hasFixedPeriod = $periode && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode);
        $chartData = $rows->map(function ($row) use ($nomorRumah, $hasFixedPeriod) {
            $rumah = $row->masterPenghuni;
            $rumahLabel = trim(($rumah?->nomor_rumah ?? '-') . ' - ' . ($rumah?->kepala_keluarga ?? '-'));

            return [
                'label' => $nomorRumah ? $row->periode : ($hasFixedPeriod ? $rumahLabel : $rumahLabel . ' (' . $row->periode . ')'),
                'nomor_rumah' => $rumah?->nomor_rumah ?? '-',
                'kepala_keluarga' => $rumah?->kepala_keluarga ?? '-',
                'periode' => $row->periode,
                'total_bayar' => (int) $row->total_bayar,
            ];
        });

        $totalBayar = (int) $rows->sum('total_bayar');
        $masterPenghunis = MasterPenghuni::query()
            ->orderBy('nomor_rumah')
            ->get(['id', 'nomor_rumah', 'kepala_keluarga']);

        return view('laporan.pembayaran', compact(
            'chartData',
            'masterPenghunis',
            'nomorRumah',
            'periode',
            'rows',
            'totalBayar'
        ));
    }
}
