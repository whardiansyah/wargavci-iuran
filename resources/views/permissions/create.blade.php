@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Permission Baru</h2>
            <small class="text-muted">Buat permission baru yang dapat di-assign ke roles</small>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-form"></i> Form Input Permission</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('permissions.store') }}" method="POST" novalidate>
                        @csrf

                        <!-- Nama Permission -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-key"></i> Nama Permission
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. create_post, edit_post, delete_post" required>
                            <small class="form-text text-muted">Gunakan format: action_object (e.g. create_user, edit_role, delete_permission)</small>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Nama Tampilan -->
                        <div class="mb-3">
                            <label for="display_name" class="form-label">
                                <i class="fas fa-tag"></i> Nama Tampilan
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name') }}" placeholder="e.g. Create Post, Edit Post, Delete Post" required>
                            @error('display_name')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left"></i> Deskripsi
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Deskripsi permission (opsional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Permission
                            </button>
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Konvensi Permission</h5>
                </div>
                <div class="card-body">
                    <p class="small"><strong>Format Naming:</strong></p>
                    <p class="small text-muted mb-3">
                        Gunakan format <code>action_object</code> untuk konsistensi
                    </p>

                    <p class="small"><strong>Contoh Actions:</strong></p>
                    <ul class="small">
                        <li><code>view</code> - Melihat data</li>
                        <li><code>create</code> - Membuat data baru</li>
                        <li><code>edit</code> - Mengubah data</li>
                        <li><code>delete</code> - Menghapus data</li>
                    </ul>

                    <hr>

                    <p class="small"><strong>Contoh Objects:</strong></p>
                    <ul class="small">
                        <li><code>user</code> - User</li>
                        <li><code>role</code> - Role</li>
                        <li><code>permission</code> - Permission</li>
                        <li><code>post</code> - Post/Artikel</li>
                    </ul>

                    <hr>

                    <p class="small mb-0"><strong>Contoh Lengkap:</strong></p>
                    <ul class="small">
                        <li><code>view_user</code></li>
                        <li><code>create_user</code></li>
                        <li><code>edit_user</code></li>
                        <li><code>delete_user</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .card-header {
        border-bottom: 1px solid #dee2e6;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }

    .form-control {
        border-radius: 4px;
        border: 1px solid #dee2e6;
        padding: 0.6rem 0.75rem;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .btn {
        border-radius: 4px;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
    }

    code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        color: #e83e8c;
    }
</style>
@endsection
