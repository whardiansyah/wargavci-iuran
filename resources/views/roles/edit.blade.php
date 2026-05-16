@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="fas fa-edit"></i> Edit Role</h2>
            <small class="text-muted">Perbarui informasi role dan permissions</small>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-form"></i> Form Edit Role</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.update', $role->id) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Nama Role -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag"></i> Nama Role
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" placeholder="e.g. admin, editor, viewer" required>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Nama Tampilan -->
                        <div class="mb-3">
                            <label for="display_name" class="form-label">
                                <i class="fas fa-user-tag"></i> Nama Tampilan
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name', $role->display_name) }}" placeholder="e.g. Administrator, Content Editor" required>
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
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Deskripsi role (opsional)">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Permissions -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-key"></i> Assign Permissions
                            </label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                @if ($permissions->count() > 0)
                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        <strong>{{ $permission->display_name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $permission->name }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-0"><i class="fas fa-info-circle"></i> Belum ada permissions</p>
                                @endif
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
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
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Role</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $role->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tampilan:</strong></td>
                            <td>{{ $role->display_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Permissions:</strong></td>
                            <td><span class="badge bg-info">{{ $role->permissions->count() }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Total Users:</strong></td>
                            <td><span class="badge bg-success">{{ $role->users->count() }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat:</strong></td>
                            <td><small>{{ $role->created_at->format('d M Y H:i') }}</small></td>
                        </tr>
                        <tr>
                            <td><strong>Diubah:</strong></td>
                            <td><small>{{ $role->updated_at->format('d M Y H:i') }}</small></td>
                        </tr>
                    </table>

                    <hr>

                    <p class="small text-muted mb-0">
                        <i class="fas fa-users"></i> Users dengan role ini:
                    </p>
                    @if ($role->users->count() > 0)
                        <ul class="small mt-2 mb-0">
                            @foreach ($role->users->take(5) as $user)
                                <li>{{ $user->name }} ({{ $user->email }})</li>
                            @endforeach
                            @if ($role->users->count() > 5)
                                <li><em>dan {{ $role->users->count() - 5 }} user lainnya</em></li>
                            @endif
                        </ul>
                    @else
                        <p class="small text-muted mb-0">Belum ada user dengan role ini</p>
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

    .form-check-input {
        margin-top: 0.3rem;
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
</style>
@endsection
