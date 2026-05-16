# Installation & Setup Guide - Authorization System

## Daftar File yang Dibuat/Dimodifikasi

### Middleware
- ✅ `app/Http/Middleware/CheckRole.php` - Middleware untuk validasi role
- ✅ `app/Http/Middleware/CheckPermission.php` - Middleware untuk validasi permission

### Policies
- ✅ `app/Policies/UserPolicy.php` - Policy untuk User resource
- ✅ `app/Policies/RolePolicy.php` - Policy untuk Role resource
- ✅ `app/Policies/PermissionPolicy.php` - Policy untuk Permission resource

### Seeders
- ✅ `database/seeders/PermissionSeeder.php` - Seeder untuk permissions
- ✅ `database/seeders/RoleSeeder.php` - Seeder untuk roles
- ✅ `database/seeders/DatabaseSeeder.php` - Updated dengan role assignment

### Configuration
- ✅ `app/Providers/AuthServiceProvider.php` - Registered policies
- ✅ `app/Http/Kernel.php` - Registered middleware aliases

### Controllers (Updated)
- ✅ `app/Http/Controllers/UserController.php` - Added authorization checks
- ✅ `app/Http/Controllers/RoleController.php` - Added authorization checks
- ✅ `app/Http/Controllers/PermissionController.php` - Added authorization checks

### Documentation
- ✅ `AUTHORIZATION.md` - Dokumentasi lengkap sistem

## Steps untuk Setup

### 1. Fresh Migration & Seeding
```bash
php artisan migrate:fresh --seed
```

Ini akan:
- Menghapus semua data lama
- Re-create semua tables
- Menjalankan semua seeders
- Membuat admin user dengan role admin
- Membuat 3 roles: admin, manager, user
- Membuat 12 permissions untuk user, role, dan permission management

### 2. Verify Installation

Buka database CLI atau tool (phpMyAdmin, SQLite Browser, dll):

**Check Permissions:**
```sql
SELECT * FROM permissions;
```
Seharusnya ada 12 records dengan permissions seperti:
- view_users, create_users, edit_users, delete_users
- view_roles, create_roles, edit_roles, delete_roles
- view_permissions, create_permissions, edit_permissions, delete_permissions

**Check Roles:**
```sql
SELECT * FROM roles;
```
Seharusnya ada 3 records:
- admin, manager, user

**Check Role Permissions:**
```sql
SELECT * FROM role_permission;
```
Admin role harus memiliki semua permissions

**Check Users:**
```sql
SELECT * FROM users;
```
Admin user harus ada dengan email: admin@admin.co.i

**Check User Roles:**
```sql
SELECT * FROM role_user;
```
Admin user harus terhubung ke admin role

### 3. Login & Test

**Login credentials:**
- Email: `admin@admin.co.i`
- Password: `tes123`

Setelah login, Anda harus memiliki akses ke:
- Users Management (http://localhost:8000/users)
- Roles Management (http://localhost:8000/roles)
- Permissions Management (http://localhost:8000/permissions)
- Profile Page (http://localhost:8000/profile)

### 4. Test Authorization

#### Test UserController Authorization
- Buka `/users` - Harus bisa view jika memiliki `view_users`
- Klik "Create User" - Harus bisa akses jika memiliki `create_users`
- Klik "Edit" pada user - Harus bisa akses jika memiliki `edit_users`
- Klik "Delete" pada user - Harus bisa akses jika memiliki `delete_users`

#### Test RoleController Authorization
- Buka `/roles` - Harus bisa view jika memiliki `view_roles`
- Klik "Create Role" - Harus bisa akses jika memiliki `create_roles`
- Klik "Edit" pada role - Harus bisa akses jika memiliki `edit_roles`
- Klik "Delete" pada role - Harus bisa akses jika memiliki `delete_roles`

#### Test PermissionController Authorization
- Buka `/permissions` - Harus bisa view jika memiliki `view_permissions`
- Klik "Create Permission" - Harus bisa akses jika memiliki `create_permissions`
- Klik "Edit" pada permission - Harus bisa akses jika memiliki `edit_permissions`
- Klik "Delete" pada permission - Harus bisa akses jika memiliki `delete_permissions`

## Testing dengan User Non-Admin

Untuk test authorization yang lebih menyeluruh, buat user dengan role "user" atau "manager":

### 1. Create Test User
```php
php artisan tinker

$user = App\Models\User::create([
    'name' => 'Test User',
    'email' => 'testuser@example.com',
    'password' => Hash::make('password'),
    'email_verified_at' => now(),
]);

$userRole = App\Models\Role::where('name', 'user')->first();
$user->roles()->attach($userRole);

exit
```

### 2. Login dengan Test User
- Email: `testuser@example.com`
- Password: `password`

### 3. Verify Restricted Access
Test User dengan role "user" hanya memiliki permissions:
- `view_users`
- `view_roles`
- `view_permissions`

Test User TIDAK bisa:
- Membuat user baru
- Edit user
- Delete user
- Membuat role/permission
- Edit role/permission
- Delete role/permission

Jika mencoba akses yang tidak authorized, akan tampil error 403 Forbidden.

## Troubleshooting

### Error: "SQLSTATE[HY000]: General error: 1 no such table"
**Solusi:** Run migration:
```bash
php artisan migrate
```

### Error: "unauthorized" / "403 Forbidden"
**Kemungkinan:**
- User tidak memiliki permission yang diperlukan
- Periksa user roles dan permissions di database
- Verify policy logic di app/Policies/

### Error: "Middleware not found"
**Solusi:** Verify middleware sudah terdaftar di app/Http/Kernel.php

### Data tidak terupdate setelah seeding
**Solusi:** Run fresh migrate:
```bash
php artisan migrate:fresh --seed
```

## Next Steps - Customize

### Tambah Permission Baru
1. Buka `database/seeders/PermissionSeeder.php`
2. Tambah permission ke array
3. Run: `php artisan migrate:fresh --seed`

### Tambah Role Baru
1. Buka `database/seeders/RoleSeeder.php`
2. Tambah role dan permission assignments
3. Run: `php artisan migrate:fresh --seed`

### Modify View Authorization
Update files di `resources/views/` dengan:
```blade
@can('create_users')
    <a href="{{ route('users.create') }}">Create User</a>
@endcan
```

## Useful Commands

```bash
# Check policies
php artisan tinker
>>> auth()->user()->can('view_users')
>>> auth()->user()->cannot('delete_users')

# Check roles
>>> auth()->user()->hasRole('admin')
>>> auth()->user()->hasAnyRole(['admin', 'manager'])

# Check permissions
>>> auth()->user()->hasPermission('view_users')

# Clear cache jika perlu
php artisan cache:clear
php artisan config:clear
```

## Documentation References

- Detailed Authorization Guide: See `AUTHORIZATION.md`
- Laravel Policy Documentation: https://laravel.com/docs/policies
- Laravel Authorization Documentation: https://laravel.com/docs/authorization
