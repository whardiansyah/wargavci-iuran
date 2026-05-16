@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <h2 class="h3">Edit Master Config</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('master_configs.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('master_configs.update', $masterConfig) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="code" class="form-label">Kode</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $masterConfig->code) }}" class="form-control @error('code') is-invalid @enderror" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="value" class="form-label">Nilai</label>
                    <input type="text" name="value" id="value" value="{{ old('value', $masterConfig->value) }}" class="form-control @error('value') is-invalid @enderror" required>
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Tipe</label>
                    <input type="text" name="type" id="type" value="{{ old('type', $masterConfig->type) }}" class="form-control @error('type') is-invalid @enderror" required>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4">{{ old('deskripsi', $masterConfig->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Perbarui</button>
            </form>
        </div>
    </div>
</div>
@endsection
