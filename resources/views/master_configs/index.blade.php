@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="h3">Kelola Master Config</h2>
        </div>
        <div class="col-md-4 text-end">
            @can('create', App\Models\MasterConfig::class)
                <a href="{{ route('master_configs.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Config
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
            <form action="{{ route('master_configs.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" placeholder="Cari kode, tipe, atau nilai" value="{{ $search ?? '' }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-8">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('master_configs.index') }}" class="btn btn-secondary btn-sm">
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
                        <th>Nilai</th>
                        <th>Tipe</th>
                        <th>Deskripsi</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($masterConfigs as $config)
                        <tr>
                            <td>{{ $config->id }}</td>
                            <td>{{ $config->code }}</td>
                            <td>{{ $config->value }}</td>
                            <td>{{ $config->type }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($config->deskripsi, 60) }}</td>
                            <td>
                                @can('view', $config)
                                    <a href="{{ route('master_configs.show', $config) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan
                                @can('update', $config)
                                    <a href="{{ route('master_configs.edit', $config) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                @can('delete', $config)
                                    <form action="{{ route('master_configs.destroy', $config) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus konfigurasi ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3">Tidak ada data konfigurasi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $masterConfigs->links() }}
    </div>
</div>
@endsection
