@extends('layouts.admin')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
<style>
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
        color: #212529;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem + 2px);
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tagihan</h5>
                    <div class="d-flex gap-2">
                        @can('create', App\Models\Tagihan::class)
                            <a href="{{ route('tagihan.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Tagihan
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
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Validasi Gagal!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row g-2 mb-3">
                        <div class="col-lg-7">
                            <form method="GET" action="{{ route('tagihan.index') }}" class="row g-2">
                                <div class="col-md-4">
                                    <input type="month" name="periode" class="form-control" value="{{ $periode ?? '' }}" placeholder="Filter periode">
                                </div>
                                <div class="col-md-5">
                                    <select name="nomor_rumah" id="select2-nomor-rumah" class="form-select" style="width: 100%;">
                                        <option value="">-- Semua Rumah --</option>
                                        @foreach ($masterPenghunis as $penghuni)
                                            <option value="{{ $penghuni->nomor_rumah }}" {{ ($nomorRumah ?? '') == $penghuni->nomor_rumah ? 'selected' : '' }}>
                                                {{ $penghuni->nomor_rumah }} - {{ $penghuni->kepala_keluarga }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    @if (request()->hasAny(['periode', 'nomor_rumah']))
                                        <a href="{{ route('tagihan.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Reset
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-5">
                            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                @can('create', App\Models\Tagihan::class)
                                    <form method="POST" action="{{ route('tagihan.generate') }}">
                                        @csrf
                                        <input type="hidden" name="periode" value="{{ $periode }}">
                                        <button type="submit" class="btn btn-success" {{ $iuranConfigs->isEmpty() ? 'disabled' : '' }}>
                                            <i class="fas fa-gears"></i> Generate
                                        </button>
                                    </form>
                                @endcan
                                @can('reset', App\Models\Tagihan::class)
                                    <form method="POST" action="{{ route('tagihan.reset') }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="periode" value="{{ $periode }}">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus semua tagihan periode {{ $periode }}?')">
                                            <i class="fas fa-rotate-left"></i> Reset
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>

                    @if ($tagihan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Rumah</th>
                                        <th>Periode</th>
                                        <th>Code</th>
                                        <th>Nilai</th>
                                        <th>Status Bayar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tagihan as $index => $item)
                                        <tr>
                                            <td>{{ $tagihan->firstItem() + $index }}</td>
                                            <td>{{ $item->masterPenghuni?->nomor_rumah ?? '-' }} / {{ $item->masterPenghuni?->kepala_keluarga ?? '-' }}</td>
                                            <td>{{ $item->periode }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>Rp {{ number_format($item->nilai, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge {{ $item->status_bayar === 'sudah' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $item->status_bayar === 'sudah' ? 'Sudah' : 'Belum' }}
                                                </span>
                                            </td>
                                            <td>
                                                @can('view', $item)
                                                    <a href="{{ route('tagihan.show', $item) }}" class="btn btn-info btn-sm" title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('update', $item)
                                                    <a href="{{ route('tagihan.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $item)
                                                    <form action="{{ route('tagihan.destroy', $item) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus tagihan ini?')">
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
                            {{ $tagihan->links() }}
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Belum ada tagihan untuk filter ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('#select2-nomor-rumah').select2({
            placeholder: '-- Semua Rumah --',
            allowClear: true,
            width: '100%',
        });

        @if (session('error_sudah'))
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Dapat Diproses',
                text: '{{ session('error_sudah') }}',
                confirmButtonColor: '#e74c3c',
            });
        @endif
    });
</script>
@endpush
