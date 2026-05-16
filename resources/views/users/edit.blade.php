@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="fas fa-user-edit"></i> Edit User</h2>
            <small class="text-muted">Perbarui informasi user</small>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-form"></i> Form Edit User</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Nama -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i> Nama Lengkap
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" required>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email
                                <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Masukkan email" required>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Roles Assignment -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-users-cog"></i> Assign Roles
                            </label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                @if ($roles && $roles->count() > 0)
                                    <div class="row">
                                        @foreach ($roles as $role)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}" {{ (isset($userRoles) && in_array($role->id, $userRoles)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                                        <strong>{{ $role->display_name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $role->name }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-0"><i class="fas fa-info-circle"></i> Belum ada roles. <a href="{{ route('roles.create') }}">Buat role terlebih dahulu</a></p>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> <strong>Catatan:</strong> Kosongkan field password jika tidak ingin mengubah password
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Password Baru
                                <span class="text-muted">(opsional)</span>
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-times-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock"></i> Konfirmasi Password Baru
                                <span class="text-muted">(opsional)</span>
                            </label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
                            @error('password_confirmation')
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
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
                    <h5 class="mb-0"><i class="fas fa-user-info"></i> Informasi User</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>ID User:</strong></td>
                            <td>#{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Roles:</strong></td>
                            <td><span class="badge bg-primary">{{ $user->roles->count() }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat:</strong></td>
                            <td><small>{{ $user->created_at->format('d M Y H:i') }}</small></td>
                        </tr>
                        <tr>
                            <td><strong>Diubah:</strong></td>
                            <td><small>{{ $user->updated_at->format('d M Y H:i') }}</small></td>
                        </tr>
                    </table>

                    <hr>

                    <p class="small text-muted mb-0">
                        <i class="fas fa-users-cog"></i> Roles saat ini:
                    </p>
                    @if ($user->roles->count() > 0)
                        <ul class="small mt-2 mb-0">
                            @foreach ($user->roles as $role)
                                <li><strong>{{ $role->display_name }}</strong> <code>{{ $role->name }}</code></li>
                            @endforeach
                        </ul>
                    @else
                        <p class="small text-muted mb-0">Belum ada role yang di-assign</p>
                    @endif

                    <hr>

                    <p class="small text-muted mb-0">
                        <i class="fas fa-lock"></i> Password akan dienkripsi secara otomatis
                    </p>
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

    .form-control.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }

    .btn {
        border-radius: 4px;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
    }

    .table-sm {
        margin-bottom: 0;
    }

    .table-sm td {
        padding: 0.5rem;
        border: none;
        font-size: 0.875rem;
    }
</style>
@endsection
