@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="h3">Detail Master Config</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('master_configs.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Kode</div>
                <div class="col-md-9">{{ $masterConfig->code }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Nilai</div>
                <div class="col-md-9">{{ $masterConfig->value }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Tipe</div>
                <div class="col-md-9">{{ $masterConfig->type }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Deskripsi</div>
                <div class="col-md-9">{{ $masterConfig->deskripsi ?? '-' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
