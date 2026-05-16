@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Data Rumah</h5>
                </div>
                <div class="card-body">
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

                    <form action="{{ route('master_penghunis.update', $masterPenghuni) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="kepala_keluarga" class="form-label">Kepala Keluarga <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kepala_keluarga') is-invalid @enderror" id="kepala_keluarga" name="kepala_keluarga" value="{{ old('kepala_keluarga', $masterPenghuni->kepala_keluarga) }}" required>
                            @error('kepala_keluarga')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kontak_person" class="form-label">Kontak Person</label>
                            <input type="text" class="form-control @error('kontak_person') is-invalid @enderror" id="kontak_person" name="kontak_person" value="{{ old('kontak_person', $masterPenghuni->kontak_person) }}">
                            @error('kontak_person')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nomor_rumah" class="form-label">Nomor Rumah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nomor_rumah') is-invalid @enderror" id="nomor_rumah" name="nomor_rumah" value="{{ old('nomor_rumah', $masterPenghuni->nomor_rumah) }}" required>
                            @error('nomor_rumah')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status_rumah" class="form-label">Status Rumah <span class="text-danger">*</span></label>
                            <select class="form-select @error('status_rumah') is-invalid @enderror" id="status_rumah" name="status_rumah" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="pribadi" {{ old('status_rumah', $masterPenghuni->status_rumah) === 'pribadi' ? 'selected' : '' }}>Pribadi</option>
                                <option value="sewa" {{ old('status_rumah', $masterPenghuni->status_rumah) === 'sewa' ? 'selected' : '' }}>Sewa</option>
                                <option value="kosong" {{ old('status_rumah', $masterPenghuni->status_rumah) === 'kosong' ? 'selected' : '' }}>Kosong</option>
                            </select>
                            @error('status_rumah')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="aktif" {{ old('status', $masterPenghuni->status ?? 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak aktif" {{ old('status', $masterPenghuni->status ?? 'aktif') === 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Perbarui
                            </button>
                            <a href="{{ route('master_penghunis.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
