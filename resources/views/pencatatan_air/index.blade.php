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
                    <h5 class="mb-0">Pencatatan Air</h5>
                    <div class="d-flex gap-2">
                        @can('export', App\Models\PencatatanAir::class)
                            <a href="{{ route('pencatatan_air.export', array_filter(['periode' => $periode ?? null, 'nomor_rumah' => $nomorRumah ?? null])) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        @endcan
                        @can('create', App\Models\PencatatanAir::class)
                            <a href="{{ route('pencatatan_air.template', array_filter(['periode' => $periode ?? null])) }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download"></i> Template Import
                            </a>
                            <a href="{{ route('pencatatan_air.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Pencatatan Air
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

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <form method="GET" action="{{ route('pencatatan_air.index') }}" class="d-flex flex-wrap gap-2">
                                <div class="input-group" style="width: auto; flex: 1;">
                                    <input type="month" name="periode" class="form-control" value="{{ $periode ?? '' }}" placeholder="Filter periode">
                                </div>
                                <div class="input-group" style="width: auto; flex: 1;">
                                    <select name="nomor_rumah" id="select2-nomor-rumah" class="form-select" style="width: 100%;">
                                        <option value="">-- Semua Rumah --</option>
                                        @foreach ($masterPenghunis as $penghuni)
                                            <option value="{{ $penghuni->nomor_rumah }}" {{ ($nomorRumah ?? '') == $penghuni->nomor_rumah ? 'selected' : '' }}>
                                                {{ $penghuni->nomor_rumah }} - {{ $penghuni->kepala_keluarga }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                @if (request()->hasAny(['periode', 'nomor_rumah']))
                                    <a href="{{ route('pencatatan_air.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                @endif
                            </form>
                        </div>
                        <div class="col-md-6 mb-2">
                            <form method="POST" action="{{ route('pencatatan_air.import') }}" enctype="multipart/form-data" class="input-group">
                                @csrf
                                <input type="file" name="file" class="form-control" accept=".xls,.xlsx" required>
                                <button class="btn btn-success" type="submit">
                                    <i class="fas fa-file-import"></i> Import XLSX
                                </button>
                            </form>
                            @error('file')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: nomor rumah, periode, meter lalu, meter kini. Template berisi rumah aktif, periode N+1, dan meter lalu dari periode sebelumnya.</small>
                        </div>
                    </div>

                    @if (session('import_errors'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Beberapa baris gagal diimport:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach (session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($pencatatanAirs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Rumah</th>
                                        <th>Periode</th>
                                        <th>Meter Lalu</th>
                                        <th>Meter Kini</th>
                                        <th>Pemakaian</th>
                                        <th>Total Tagihan Air</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pencatatanAirs as $index => $item)
                                        <tr>
                                            <td>{{ $pencatatanAirs->firstItem() + $index }}</td>
                                            <td>{{ $item->masterPenghuni?->nomor_rumah ?? '-' }} / {{ $item->masterPenghuni?->kepala_keluarga ?? '-' }}</td>
                                            <td>{{ $item->periode_label }}</td>
                                            <td>{{ number_format($item->meter_lalu, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->meter_kini, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->pemakaian, 2, ',', '.') }}</td>
                                            <td>Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</td>
                                            <td>
                                                @can('view', $item)
                                                    <a href="{{ route('pencatatan_air.show', $item) }}" class="btn btn-info btn-sm" title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('update', $item)
                                                    <a href="{{ route('pencatatan_air.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $item)
                                                    <form action="{{ route('pencatatan_air.destroy', $item) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
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
                            {{ $pencatatanAirs->links() }}
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Belum ada pencatatan air. <a href="{{ route('pencatatan_air.create') }}" class="alert-link">Tambah data sekarang</a>
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
<script>
    $(document).ready(function () {
        $('#select2-nomor-rumah').select2({
            placeholder: '-- Semua Rumah --',
            allowClear: true,
            width: '100%',
        });
    });
</script>
@endpush
