@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="fas fa-edit"></i> Edit Permission</h2>
            <small class="text-muted">Perbarui informasi permission</small>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-form"></i> Form Edit Permission</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('permissions.update', $permission->id) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Nama Permission -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-key"></i> Nama Permission
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $permission->name) }}" placeholder="e.g. create_post, edit_post, delete_post" required>
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
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name', $permission->display_name) }}" placeholder="e.g. Create Post, Edit Post, Delete Post" required>
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
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Deskripsi permission (opsional)">{{ old('description', $permission->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
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
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Permission</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $permission->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tampilan:</strong></td>
                            <td>{{ $permission->display_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Roles:</strong></td>
                            <td><span class="badge bg-warning text-dark">{{ $permission->roles->count() }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat:</strong></td>
                            <td><small>{{ $permission->created_at->format('d M Y H:i') }}</small></td>
                        </tr>
                        <tr>
                            <td><strong>Diubah:</strong></td>
                            <td><small>{{ $permission->updated_at->format('d M Y H:i') }}</small></td>
                        </tr>
                    </table>

                    <hr>

                    <p class="small text-muted mb-0">
                        <i class="fas fa-users-cog"></i> Roles dengan permission ini:
                    </p>
                    @if ($permission->roles->count() > 0)
                        <ul class="small mt-2 mb-0">
                            @foreach ($permission->roles->take(5) as $role)
                                <li>{{ $role->display_name }} <code>{{ $role->name }}</code></li>
                            @endforeach
                            @if ($permission->roles->count() > 5)
                                <li><em>dan {{ $permission->roles->count() - 5 }} role lainnya</em></li>
                            @endif
                        </ul>
                    @else
                        <p class="small text-muted mb-0">Belum ada role dengan permission ini</p>
                    @endif
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

    .btn {
        border-radius: 4px;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
    }

    .table-sm td {
        padding: 0.5rem;
        border: none;
        font-size: 0.875rem;
    }

    code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        color: #e83e8c;
    }
</style>
@endsection
