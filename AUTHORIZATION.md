# Sistem Authorization & Role-Based Access Control (RBAC)

Dokumentasi lengkap sistem authorization dan RBAC untuk aplikasi ini.

## Daftar Roles

### 1. Admin (administrator)
- **Deskripsi**: Administrator memiliki akses penuh ke semua fitur aplikasi
- **Permissions**: Semua permissions yang tersedia

### 2. Manager (manager)
- **Deskripsi**: Manajer dapat mengelola pengguna dan peran
- **Permissions**:
  - view_users
  - create_users
  - edit_users
  - view_roles
  - view_permissions

### 3. User (user)
- **Deskripsi**: Pengguna biasa dengan akses terbatas
- **Permissions**:
  - view_users
  - view_roles
  - view_permissions

## Daftar Permissions

### User Management
| Permission | Display Name | Deskripsi |
|-----------|-------------|-----------|
| view_users | Lihat Pengguna | Izin untuk melihat daftar pengguna |
| create_users | Buat Pengguna | Izin untuk membuat pengguna baru |
| edit_users | Edit Pengguna | Izin untuk mengedit pengguna |
| delete_users | Hapus Pengguna | Izin untuk menghapus pengguna |

### Role Management
| Permission | Display Name | Deskripsi |
|-----------|-------------|-----------|
| view_roles | Lihat Peran | Izin untuk melihat daftar peran |
| create_roles | Buat Peran | Izin untuk membuat peran baru |
| edit_roles | Edit Peran | Izin untuk mengedit peran |
| delete_roles | Hapus Peran | Izin untuk menghapus peran |

### Permission Management
| Permission | Display Name | Deskripsi |
|-----------|-------------|-----------|
| view_permissions | Lihat Izin | Izin untuk melihat daftar izin |
| create_permissions | Buat Izin | Izin untuk membuat izin baru |
| edit_permissions | Edit Izin | Izin untuk mengedit izin |
| delete_permissions | Hapus Izin | Izin untuk menghapus izin |

## Komponen Authorization System

### 1. Middleware
- **CheckRole** (`app/Http/Middleware/CheckRole.php`)
  - Memvalidasi bahwa user memiliki role tertentu
  - Digunakan untuk route-level authorization
  - Contoh: `Route::middleware('check.role:admin,manager')`

- **CheckPermission** (`app/Http/Middleware/CheckPermission.php`)
  - Memvalidasi bahwa user memiliki permission tertentu
  - Digunakan untuk route-level authorization
  - Contoh: `Route::middleware('check.permission:view_users')`

### 2. Policies
- **UserPolicy** (`app/Policies/UserPolicy.php`)
  - Mengontrol akses ke User resource
  - Methods: viewAny, view, create, update, delete

- **RolePolicy** (`app/Policies/RolePolicy.php`)
  - Mengontrol akses ke Role resource
  - Methods: viewAny, view, create, update, delete

- **PermissionPolicy** (`app/Policies/PermissionPolicy.php`)
  - Mengontrol akses ke Permission resource
  - Methods: viewAny, view, create, update, delete

### 3. Model Methods
Pada model User:
- `hasRole($role)` - Cek apakah user memiliki role
- `hasAnyRole($roles)` - Cek apakah user memiliki salah satu dari beberapa role
- `hasPermission($permission)` - Cek apakah user memiliki permission

## Cara Menggunakan

### Authorization di Controller
```php
// Cek apakah user memiliki permission untuk melihat list users
public function index()
{
    $this->authorize('viewAny', User::class);
    // ...
}

// Cek apakah user memiliki permission untuk edit user tertentu
public function update(Request $request, User $user)
{
    $this->authorize('update', $user);
    // ...
}
```

### Authorization di Route
```php
// Menggunakan middleware check.role
Route::middleware('check.role:admin,manager')->group(function () {
    Route::resource('users', UserController::class);
});

// Menggunakan middleware check.permission
Route::middleware('check.permission:view_users')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
```

### Authorization di View
```blade
@can('view_users')
    <!-- Tampilkan daftar users -->
@endcan

@can('create_users')
    <a href="{{ route('users.create') }}">Buat User Baru</a>
@endcan

@cannot('delete_users')
    <!-- Tombol delete disembunyikan -->
@endcannot
```

### Check Di User Model
```php
$user = Auth::user();

// Cek role
if ($user->hasRole('admin')) {
    // User adalah admin
}

// Cek permission
if ($user->hasPermission('edit_users')) {
    // User dapat mengedit users
}
```

## Database Seeding

Untuk menjalankan seeding dan membuat roles, permissions, dan admin user:

```bash
php artisan migrate:fresh --seed
```

Atau hanya menjalankan seeder tertentu:

```bash
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
```

## Login Credentials

User default yang dibuat saat seeding:

```
Email: admin@admin.co.i
Password: tes123
Role: Admin (memiliki semua permissions)
```

## Menambahkan Permissions Baru

1. Buat permission baru via admin panel atau gunakan Tinker:
```bash
php artisan tinker
>>> App\Models\Permission::create(['name' => 'permission_name', 'display_name' => 'Permission Display Name'])
```

2. Assign permission ke role:
```php
$role = App\Models\Role::find(1);
$permission = App\Models\Permission::where('name', 'permission_name')->first();
$role->permissions()->attach($permission);
```

## Menambahkan Role Baru

```php
$role = App\Models\Role::create([
    'name' => 'role_name',
    'display_name' => 'Role Display Name',
    'description' => 'Role description'
]);

// Assign permissions
$permissions = App\Models\Permission::whereIn('name', ['permission1', 'permission2'])->pluck('id');
$role->permissions()->sync($permissions);
```

## Error Handling

Jika user mencoba mengakses resource yang tidak memiliki permission:
- Status Code: 403 (Forbidden)
- Message: "Anda tidak memiliki akses ke halaman ini."
