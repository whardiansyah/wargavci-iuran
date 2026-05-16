@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Data Rumah</h5>
                    <div>
                        @can('update', $masterPenghuni)
                            <a href="{{ route('master_penghunis.edit', $masterPenghuni) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                        @can('delete', $masterPenghuni)
                            <form action="{{ route('master_penghunis.destroy', $masterPenghuni) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="text-muted">Kepala Keluarga</label>
                        </div>
                        <div class="col-md-9">
                            <strong>{{ $masterPenghuni->kepala_keluarga }}</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="text-muted">Kontak Person</label>
                        </div>
                        <div class="col-md-9">
                            <strong>{{ $masterPenghuni->kontak_person ?? '-' }}</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="text-muted">Nomor Rumah</label>
                        </div>
                        <div class="col-md-9">
                            <strong>{{ $masterPenghuni->nomor_rumah }}</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="text-muted">Status Rumah</label>
                        </div>
                        <div class="col-md-9">
                            <span class="badge bg-{{ $masterPenghuni->status_color }}">
                                {{ $masterPenghuni->status_label }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="text-muted">Status</label>
                        </div>
                        <div class="col-md-9">
                            <span class="badge bg-{{ $masterPenghuni->status_aktif_color }}">
                                {{ $masterPenghuni->status_aktif_label }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="text-muted">Tanggal Dibuat</label>
                        </div>
                        <div class="col-md-9">
                            <strong>{{ $masterPenghuni->created_at->format('d M Y H:i') }}</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="text-muted">Terakhir Diperbarui</label>
                        </div>
                        <div class="col-md-9">
                            <strong>{{ $masterPenghuni->updated_at->format('d M Y H:i') }}</strong>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('master_penghunis.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
