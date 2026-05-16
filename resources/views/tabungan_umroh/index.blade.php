@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tabungan Umroh</h5>
                    @can('create', App\Models\TabunganUmroh::class)
                        <a href="{{ route('tabungan_umroh.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Tabungan
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

                    <form method="GET" action="{{ route('tabungan_umroh.index') }}" class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" name="nama_anggota" class="form-control" value="{{ $namaAnggota ?? '' }}" placeholder="Filter nama anggota">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            @if (request()->has('nama_anggota') && request('nama_anggota') !== '')
                                <a href="{{ route('tabungan_umroh.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            @endif
                        </div>
                    </form>

                    @if ($tabunganUmroh->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Anggota</th>
                                        <th>Tanggal</th>
                                        <th>Nominal</th>
                                        <th>Cara Setor</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tabunganUmroh as $index => $item)
                                        <tr>
                                            <td>{{ $tabunganUmroh->firstItem() + $index }}</td>
                                            <td>{{ $item->anggota->nama ?? '-' }}</td>
                                            <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                                            <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                            <td>{{ $item->cara_setor }}</td>
                                            <td>{{ $item->keterangan ?? '-' }}</td>
                                            <td>
                                                @can('view', $item)
                                                    <a href="{{ route('tabungan_umroh.show', $item) }}" class="btn btn-info btn-sm" title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('update', $item)
                                                    <a href="{{ route('tabungan_umroh.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $item)
                                                    <form action="{{ route('tabungan_umroh.destroy', $item) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data tabungan ini?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $tabunganUmroh->links() }}
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Belum ada data tabungan umroh.
                            @can('create', App\Models\TabunganUmroh::class)
                                <a href="{{ route('tabungan_umroh.create') }}" class="alert-link">Tambah data sekarang</a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
