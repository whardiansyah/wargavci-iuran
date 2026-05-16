@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="h3">Daftar Penyewa</h2>
        </div>
        <div class="col-md-4 text-end">
            @can('create', App\Models\Penyewa::class)
            <a href="{{ route('penyewa.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Penyewa
            </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Form -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('penyewa.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" placeholder="Cari nama penyewa atau penghuni" 
                           value="{{ request('search') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak aktif" {{ request('status') == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('penyewa.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nomor Rumah</th>
                        <th>Nama Penyewa</th>
                        <th>Mulai Sewa</th>
                        <th>Selesai Sewa</th>
                        <th>Jml Anggota</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penyewas as $penyewa)
                        <tr>
                            <td>{{ $penyewa->id }}</td>
                            <td>{{ $penyewa->masterPenghuni->nomor_rumah ?? 'N/A' }}</td>
                            <td>{{ $penyewa->nama_penyewa }}</td>
                            <td>{{ \Carbon\Carbon::parse($penyewa->tgl_mulai_sewa)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($penyewa->tgl_selesai_sewa)->format('d-m-Y') }}</td>
                            <td>{{ $penyewa->jml_anggota }} orang</td>
                            <td>
                                <span class="badge {{ $penyewa->status == 'aktif' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($penyewa->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('penyewa.show', $penyewa->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('penyewa.edit', $penyewa->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('penyewa.destroy', $penyewa->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-3">Tidak ada data penyewa</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $penyewas->links() }}
    </div>
</div>
@endsection
