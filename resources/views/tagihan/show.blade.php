@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Tagihan</h5>
                    <a href="{{ route('tagihan.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Rumah</th>
                            <td>{{ $tagihan->masterPenghuni?->nomor_rumah ?? '-' }} / {{ $tagihan->masterPenghuni?->kepala_keluarga ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Periode</th>
                            <td>{{ $tagihan->periode }}</td>
                        </tr>
                        <tr>
                            <th>Code</th>
                            <td>{{ $tagihan->code }}</td>
                        </tr>
                        <tr>
                            <th>Nilai</th>
                            <td>Rp {{ number_format($tagihan->nilai, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Status Bayar</th>
                            <td>{{ $tagihan->status_bayar === 'sudah' ? 'Sudah' : 'Belum' }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $tagihan->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Diperbarui</th>
                            <td>{{ $tagihan->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
