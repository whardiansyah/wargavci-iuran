@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transaksi Kas</h5>
                    <div class="d-flex gap-2">
                        @can('export', App\Models\TransaksiKas::class)
                            <a href="{{ route('transaksi_kas.export', array_filter(['tanggal_mulai' => $tanggalMulai ?? null, 'tanggal_selesai' => $tanggalSelesai ?? null, 'deskripsi' => $deskripsi ?? null])) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        @endcan
                        @can('create', App\Models\TransaksiKas::class)
                            <a href="{{ route('transaksi_kas.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Transaksi Kas
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="GET" action="{{ route('transaksi_kas.index') }}" class="row g-2 mb-3">
                        <div class="col-md-3">
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggalMulai ?? '' }}" placeholder="Tanggal mulai">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggalSelesai ?? '' }}" placeholder="Tanggal selesai">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="deskripsi" class="form-control" value="{{ $deskripsi ?? '' }}" placeholder="Filter deskripsi">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            @if (request()->hasAny(['tanggal_mulai', 'tanggal_selesai', 'deskripsi']))
                                <a href="{{ route('transaksi_kas.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            @endif
                        </div>
                    </form>

                    @if ($transaksiKas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kode</th>
                                        <th>Deskripsi</th>
                                        <th>Kredit</th>
                                        <th>Debet</th>
                                        <th>Saldo</th>
                                        <th>Jenis</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaksiKas as $index => $item)
                                        <tr>
                                            <td>{{ $transaksiKas->firstItem() + $index }}</td>
                                            <td>{{ $item->tanggal?->format('d/m/Y') ?? '-' }}</td>
                                            <td>{{ $item->kode ?? '-' }}</td>
                                            <td>{{ $item->deskripsi }}</td>
                                            <td>Rp {{ number_format($item->kredit, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($item->debet, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($item->saldo_berjalan, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge {{ $item->jenis === 'saldo_awal' ? 'bg-info' : 'bg-secondary' }}">
                                                    {{ $item->jenis === 'saldo_awal' ? 'Saldo Awal' : 'Transaksi' }}
                                                </span>
                                            </td>
                                            <td>
                                                @can('view', $item)
                                                    <a href="{{ route('transaksi_kas.show', $item) }}" class="btn btn-info btn-sm" title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('update', $item)
                                                    <a href="{{ route('transaksi_kas.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $item)
                                                    <form action="{{ route('transaksi_kas.destroy', $item) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi kas ini?')">
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
                            {{ $transaksiKas->links() }}
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Belum ada transaksi kas.
                            @can('create', App\Models\TransaksiKas::class)
                                <a href="{{ route('transaksi_kas.create') }}" class="alert-link">Tambah data sekarang</a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
