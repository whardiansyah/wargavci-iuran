@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="h3">Detail Penyewa: {{ $penyewa->nama_penyewa }}</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('penyewa.edit', $penyewa->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('penyewa.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ID</label>
                        </div>
                        <div class="col-md-8">
                            <p>{{ $penyewa->id }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Nomor Rumah</label>
                        </div>
                        <div class="col-md-8">
                            <p>{{ $penyewa->masterPenghuni->nomor_rumah ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Nama Penyewa</label>
                        </div>
                        <div class="col-md-8">
                            <p>{{ $penyewa->nama_penyewa }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Mulai Sewa</label>
                        </div>
                        <div class="col-md-8">
                            <p>{{ \Carbon\Carbon::parse($penyewa->tgl_mulai_sewa)->format('d-m-Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Selesai Sewa</label>
                        </div>
                        <div class="col-md-8">
                            <p>{{ \Carbon\Carbon::parse($penyewa->tgl_selesai_sewa)->format('d-m-Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Jumlah Anggota</label>
                        </div>
                        <div class="col-md-8">
                            <p>{{ $penyewa->jml_anggota }} orang</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status</label>
                        </div>
                        <div class="col-md-8">
                            <span class="badge {{ $penyewa->status == 'aktif' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($penyewa->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Dibuat Pada</label>
                        </div>
                        <div class="col-md-8">
                            <p>{{ $penyewa->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Diperbarui Pada</label>
                        </div>
                        <div class="col-md-8">
                            <p>{{ $penyewa->updated_at->format('d-m-Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('penyewa.edit', $penyewa->id) }}" class="btn btn-warning btn-sm w-100 mb-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('penyewa.destroy', $penyewa->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100" 
                                onclick="return confirm('Yakin ingin menghapus?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                    <a href="{{ route('penyewa.index') }}" class="btn btn-secondary btn-sm w-100 mt-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
