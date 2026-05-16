@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="h3">Tambah Penyewa Baru</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('penyewa.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="master_penghuni_id" class="form-label">Nomor Rumah <span class="text-danger">*</span></label>
                    <select name="master_penghuni_id" id="master_penghuni_id" 
                            class="form-select @error('master_penghuni_id') is-invalid @enderror">
                        <option value="">Pilih Nomor Rumah</option>
                        @foreach ($masterPenghunis as $penghuni)
                            <option value="{{ $penghuni->id }}" {{ old('master_penghuni_id') == $penghuni->id ? 'selected' : '' }}>
                                {{ $penghuni->nomor_rumah }}
                            </option>
                        @endforeach
                    </select>
                    @error('master_penghuni_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nama_penyewa" class="form-label">Nama Penyewa <span class="text-danger">*</span></label>
                    <input type="text" name="nama_penyewa" id="nama_penyewa" 
                           class="form-control @error('nama_penyewa') is-invalid @enderror"
                           value="{{ old('nama_penyewa') }}" placeholder="Masukkan nama penyewa">
                    @error('nama_penyewa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tgl_mulai_sewa" class="form-label">Tanggal Mulai Sewa <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_mulai_sewa" id="tgl_mulai_sewa" 
                               class="form-control @error('tgl_mulai_sewa') is-invalid @enderror"
                               value="{{ old('tgl_mulai_sewa') }}">
                        @error('tgl_mulai_sewa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tgl_selesai_sewa" class="form-label">Tanggal Selesai Sewa <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_selesai_sewa" id="tgl_selesai_sewa" 
                               class="form-control @error('tgl_selesai_sewa') is-invalid @enderror"
                               value="{{ old('tgl_selesai_sewa') }}">
                        @error('tgl_selesai_sewa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jml_anggota" class="form-label">Jumlah Anggota <span class="text-danger">*</span></label>
                        <input type="number" name="jml_anggota" id="jml_anggota" 
                               class="form-control @error('jml_anggota') is-invalid @enderror"
                               value="{{ old('jml_anggota') }}" placeholder="Contoh: 5" min="1">
                        @error('jml_anggota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" 
                                class="form-select @error('status') is-invalid @enderror">
                            <option value="">Pilih Status</option>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak aktif" {{ old('status') == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('penyewa.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
