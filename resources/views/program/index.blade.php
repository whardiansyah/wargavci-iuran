@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="h3">Program</h2>
        </div>
        <div class="col-md-4 text-end">
            @can('create', App\Models\Program::class)
                <a href="{{ route('program.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Program
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

    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('program.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" placeholder="Cari kode, nama, atau deskripsi" value="{{ $search ?? '' }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ ($status ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak aktif" {{ ($status ?? '') === 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('program.index') }}" class="btn btn-secondary btn-sm">
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
                        <th>Kode</th>
                        <th>Nama Program</th>
                        <th>Jumlah Anggota</th>
                        <th>Status</th>
                        <th width="170">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($program as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->kode ?? '-' }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->anggota_count }}</td>
                            <td>
                                <span class="badge {{ $item->status === 'aktif' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>
                                @can('view', $item)
                                    <a href="{{ route('program.show', $item) }}" class="btn btn-info btn-sm" title="Lihat Anggota">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan
                                @can('update', $item)
                                    <a href="{{ route('program.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                @can('delete', $item)
                                    <form action="{{ route('program.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Yakin ingin menghapus program ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3">Tidak ada data program</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $program->links() }}
    </div>
</div>
@endsection
