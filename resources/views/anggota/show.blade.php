@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="h3">Detail Anggota Umroh</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('anggota.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @can('update', $anggota)
                <a href="{{ route('anggota.edit', $anggota) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endcan
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <tbody>
                    <tr>
                        <th style="width: 220px;">Nama</th>
                        <td>{{ $anggota->nama }}</td>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <td>{{ $anggota->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td>{{ $anggota->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <th>Tempat/Tanggal Lahir</th>
                        <td>{{ $anggota->tempat_lahir ?? '-' }} / {{ $anggota->tanggal_lahir?->format('d-m-Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>No HP</th>
                        <td>{{ $anggota->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge {{ $anggota->status === 'aktif' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($anggota->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $anggota->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $anggota->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $anggota->created_at?->format('d-m-Y H:i') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ $anggota->updated_at?->format('d-m-Y H:i') ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
