@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Rumah</h5>
                    @can('create', App\Models\MasterPenghuni::class)
                        <a href="{{ route('master_penghunis.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Data Rumah
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Search Box -->
                    <div class="row mb-3">
                        <div class="col-md-9">
                            <form method="GET" action="{{ route('master_penghunis.index') }}" class="row g-2">
                                <div class="col-md-7">
                                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan Kepala Keluarga, Kontak Person, atau Nomor Rumah..." value="{{ $search ?? '' }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="aktif" {{ ($status ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="tidak aktif" {{ ($status ?? '') === 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-secondary w-100" type="submit">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                                @if ($search || $status)
                                    <div class="col-md-auto">
                                        <a href="{{ route('master_penghunis.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Reset
                                        </a>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>

                    @if ($masterPenghunis->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kepala Keluarga</th>
                                        <th>Kontak Person</th>
                                        <th>Nomor Rumah</th>
                                        <th>Status Rumah</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($masterPenghunis as $index => $penghuni)
                                        <tr>
                                            <td>{{ $masterPenghunis->firstItem() + $index }}</td>
                                            <td>{{ $penghuni->kepala_keluarga }}</td>
                                            <td>{{ $penghuni->kontak_person ?? '-' }}</td>
                                            <td>{{ $penghuni->nomor_rumah }}</td>
                                            <td>
                                                <span class="badge bg-{{ $penghuni->status_color }}">
                                                    {{ $penghuni->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $penghuni->status_aktif_color }}">
                                                    {{ $penghuni->status_aktif_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @can('view', $penghuni)
                                                    <a href="{{ route('master_penghunis.show', $penghuni) }}" class="btn btn-info btn-sm" title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('update', $penghuni)
                                                    <a href="{{ route('master_penghunis.edit', $penghuni) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $penghuni)
                                                    <form action="{{ route('master_penghunis.destroy', $penghuni) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                Tidak ada data
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $masterPenghunis->links() }}
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Belum ada data rumah. <a href="{{ route('master_penghunis.create') }}" class="alert-link">Tambah data sekarang</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
