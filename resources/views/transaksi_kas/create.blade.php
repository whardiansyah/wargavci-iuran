@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <h2 class="h3">Tambah Transaksi Kas</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('transaksi_kas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('transaksi_kas.store') }}" method="POST">
                @csrf
                @include('transaksi_kas._form')
            </form>
        </div>
    </div>
</div>
@endsection
