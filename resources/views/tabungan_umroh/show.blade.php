@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="h3">Detail Tabungan Umroh</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('tabungan_umroh.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card" style="max-width: 640px;">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Anggota</div>
                <div class="col-md-9">{{ $tabunganUmroh->anggota->nama ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Tanggal</div>
                <div class="col-md-9">{{ $tabunganUmroh->tanggal->format('d/m/Y') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Nominal</div>
                <div class="col-md-9">Rp {{ number_format($tabunganUmroh->nominal, 0, ',', '.') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Cara Setor</div>
                <div class="col-md-9">{{ $tabunganUmroh->cara_setor }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Keterangan</div>
                <div class="col-md-9">{{ $tabunganUmroh->keterangan ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
