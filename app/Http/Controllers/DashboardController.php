<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\PencatatanAir;
use App\Models\Tagihan;
use App\Models\TransaksiKas;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $transaksiKasTipe = $request->get('transaksi_kas_tipe', 'all');
        if (!in_array($transaksiKasTipe, ['all', 'kredit', 'debet'], true)) {
            $transaksiKasTipe = 'all';
        }

        $dailySalesPeriode = $request->get('daily_sales_periode', now()->format('Y-m'));

        $dailySalesChart = Pembayaran::query()
            ->where('periode', $dailySalesPeriode)
            ->whereNotNull('cara_bayar')
            ->selectRaw('cara_bayar, SUM(jumlah_bayar) as total_bayar')
            ->groupBy('cara_bayar')
            ->orderBy('cara_bayar')
            ->get();

        $periodeList = Pembayaran::query()
            ->whereNotNull('periode')
            ->distinct()
            ->orderBy('periode', 'desc')
            ->pluck('periode');

        $tagihanPeriode = $request->get('tagihan_periode', now()->format('Y-m'));
        $tagihanPeriodeList = Tagihan::query()
            ->whereNotNull('periode')
            ->distinct()
            ->orderBy('periode', 'desc')
            ->pluck('periode');

        $jenisTagihanChart = Tagihan::query()
            ->where('periode', $tagihanPeriode)
            ->where('status_bayar', 'sudah')
            ->selectRaw('code, SUM(nilai) as total_bayar')
            ->groupBy('code')
            ->orderBy('code')
            ->get();

        $saldoKas = TransaksiKas::query()
            ->selectRaw('SUM(kredit) - SUM(debet) as saldo')
            ->value('saldo') ?? 0;

        $kasBulanBerjalan = TransaksiKas::query()
            ->where('periode_bulan', now()->month)
            ->where('periode_tahun', now()->year)
            ->selectRaw('SUM(kredit) as kas_masuk, SUM(debet) as kas_keluar')
            ->first();

        $saldoAwal = TransaksiKas::query()
            ->where('periode_bulan', now()->month)
            ->where('periode_tahun', now()->year)
            ->where('jenis', 'saldo_awal')
            ->selectRaw('saldo')
            ->first();

        $pendapatanIuranAir = PencatatanAir::query()
            ->join('tagihan', function ($join) {
                $join->on('tagihan.master_penghuni_id', '=', 'pencatatan_air.master_penghuni_id')
                    ->where('tagihan.periode', now()->format('Y-m'))
                    ->where('tagihan.code', 'iuran-air')
                    ->where('tagihan.status_bayar', 'sudah');
            })
            ->where('pencatatan_air.periode_bulan', now()->month)
            ->where('pencatatan_air.periode_tahun', now()->year)
            ->sum('tagihan.nilai');

        $penggunaanAirChart = PencatatanAir::query()
            ->selectRaw('periode_tahun, periode_bulan, SUM(meter_kini - meter_lalu) as total_pemakaian')
            ->where(function ($q) {
                $q->where('periode_tahun', '>', now()->subYear()->year)
                  ->orWhere(function ($q2) {
                      $q2->where('periode_tahun', now()->subYear()->year)
                         ->where('periode_bulan', '>=', now()->subYear()->month);
                  });
            })
            ->groupBy('periode_tahun', 'periode_bulan')
            ->orderBy('periode_tahun')
            ->orderBy('periode_bulan')
            ->get()
            ->map(fn ($row) => [
                'label' => sprintf('%02d/%04d', $row->periode_bulan, $row->periode_tahun),
                'total' => round((float) $row->total_pemakaian, 2),
            ]);

        $transaksiKasChart = TransaksiKas::query()
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->where('jenis', 'transaksi')
            ->whereNotNull('kode')
            ->selectRaw('kode, SUM(kredit) as total_kredit, SUM(debet) as total_debet')
            ->groupBy('kode')
            ->orderBy('kode')
            ->get();

        $transaksiKasList = TransaksiKas::query()
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->where('jenis', 'transaksi')
            ->when($transaksiKasTipe === 'kredit', fn ($query) => $query->where('kredit', '>', 0))
            ->when($transaksiKasTipe === 'debet', fn ($query) => $query->where('debet', '>', 0))
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $tahunList = TransaksiKas::query()
            ->whereNotNull('periode_tahun')
            ->distinct()
            ->orderBy('periode_tahun', 'desc')
            ->pluck('periode_tahun');

        return view('dashboard', compact(
            'transaksiKasChart', 'transaksiKasList', 'bulan', 'tahun', 'tahunList', 'transaksiKasTipe',
            'dailySalesChart', 'dailySalesPeriode', 'periodeList',
            'jenisTagihanChart', 'tagihanPeriode', 'tagihanPeriodeList',
            'saldoKas', 'kasBulanBerjalan', 'pendapatanIuranAir',
            'penggunaanAirChart', 'saldoAwal'
        ));
    }

    public function metodeBayarDetail(Request $request)
    {
        $periode = $request->get('periode', now()->format('Y-m'));
        $caraBayar = $request->get('cara_bayar');

        $detail = Pembayaran::query()
            ->with('masterPenghuni')
            ->where('periode', $periode)
            ->where('cara_bayar', $caraBayar)
            ->orderBy('tanggal_bayar')
            ->get()
            ->map(fn($p) => [
                'nama'          => $p->masterPenghuni?->kepala_keluarga ?? '-',
                'nomor_rumah'   => $p->masterPenghuni?->nomor_rumah ?? '-',
                'tanggal_bayar' => $p->tanggal_bayar?->format('d/m/Y') ?? '-',
                'jumlah_tagihan'=> $p->jumlah_tagihan,
                'jumlah_bayar'  => $p->jumlah_bayar,
            ]);

        return response()->json($detail);
    }

    public function transaksiKasDetail(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $kode = $request->get('kode');

        $detail = TransaksiKas::query()
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->where('jenis', 'transaksi')
            ->when($kode, fn ($query) => $query->where('kode', $kode))
            ->orderBy('tanggal')
            ->get()
            ->map(fn ($t) => [
                'tanggal' => $t->tanggal?->format('d/m/Y') ?? '-',
                'kode' => $t->kode ?? '-',
                'deskripsi' => $t->deskripsi ?? '-',
                'keterangan' => $t->keterangan ?? '-',
                'kredit' => $t->kredit ?? 0,
                'debet' => $t->debet ?? 0,
                'nomor_ref' => $t->nomor_ref ?? '-',
            ]);

        return response()->json($detail);
    }
}
