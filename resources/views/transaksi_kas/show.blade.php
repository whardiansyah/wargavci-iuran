@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="h3">Detail Transaksi Kas</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('transaksi_kas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Tanggal</div>
                <div class="col-md-9">{{ $transaksiKas->tanggal?->format('d/m/Y') ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Kode</div>
                <div class="col-md-9">{{ $transaksiKas->kode ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Deskripsi</div>
                <div class="col-md-9">{{ $transaksiKas->deskripsi }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Keterangan</div>
                <div class="col-md-9">{{ $transaksiKas->keterangan ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Kredit</div>
                <div class="col-md-9">Rp {{ number_format($transaksiKas->kredit, 0, ',', '.') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Debet</div>
                <div class="col-md-9">Rp {{ number_format($transaksiKas->debet, 0, ',', '.') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Saldo</div>
                <div class="col-md-9">Rp {{ number_format($transaksiKas->saldo, 0, ',', '.') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Nomor Ref</div>
                <div class="col-md-9">{{ $transaksiKas->nomor_ref ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Jenis</div>
                <div class="col-md-9">{{ $transaksiKas->jenis === 'saldo_awal' ? 'Saldo Awal' : 'Transaksi' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Periode</div>
                <div class="col-md-9">
                    {{ $transaksiKas->periode_tahun && $transaksiKas->periode_bulan ? sprintf('%04d-%02d', $transaksiKas->periode_tahun, $transaksiKas->periode_bulan) : '-' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
