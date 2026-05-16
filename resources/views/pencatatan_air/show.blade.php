@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Pencatatan Air</h5>
                    <a href="{{ route('pencatatan_air.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Rumah</th>
                            <td>{{ $pencatatanAir->masterPenghuni?->nomor_rumah ?? '-' }} / {{ $pencatatanAir->masterPenghuni?->kepala_keluarga ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Periode</th>
                            <td>{{ $pencatatanAir->periode_label }}</td>
                        </tr>
                        <tr>
                            <th>Meter Lalu</th>
                            <td>{{ number_format($pencatatanAir->meter_lalu, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Meter Kini</th>
                            <td>{{ number_format($pencatatanAir->meter_kini, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Pemakaian</th>
                            <td>{{ number_format($pencatatanAir->pemakaian, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Total Tagihan Air</th>
                            <td>Rp {{ number_format($pencatatanAir->total_tagihan_air, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $pencatatanAir->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diubah</th>
                            <td>{{ $pencatatanAir->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
