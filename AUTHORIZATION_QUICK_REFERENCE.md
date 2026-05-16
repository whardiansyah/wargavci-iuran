# Authorization System - Quick Reference Guide

## 🎯 Quick Checks

### Check Role (di Code)
```php
// Cek apakah user punya role tertentu
if (auth()->user()->hasRole('admin')) {
    // Do something
}

// Cek apakah user punya salah satu dari beberapa roles
if (auth()->user()->hasAnyRole(['admin', 'manager'])) {
    // Do something
}
```

### Check Permission (di Code)
```php
// Cek apakah user punya permission tertentu
if (auth()->user()->hasPermission('edit_users')) {
    // Do something
}
```

## 🛡️ Authorization di Controller

### Authorize View (List)
```php
public function index()
{
    $this->authorize('viewAny', User::class);
    $users = User::all();
    return view('users.index', compact('users'));
}
```

### Authorize Show (Detail)
```php
public function show(User $user)
{
    $this->authorize('view', $user);
    return view('users.show', compact('user'));
}
```

### Authorize Create
```php
public function create()
{
    $this->authorize('create', User::class);
    return view('users.create');
}
```

### Authorize Store
```php
public function store(Request $request)
{
    $this->authorize('create', User::class);
    // Validate and save
}
```

### Authorize Edit
```php
public function edit(User $user)
{
    $this->authorize('update', $user);
    return view('users.edit', compact('user'));
}
```

### Authorize Update
```php
public function update(Request $request, User $user)
{
    $this->authorize('update', $user);
    // Update and save
}
```

### Authorize Delete
```php
public function destroy(User $user)
{
    $this->authorize('delete', $user);
    $user->delete();
}
```

## 📍 Authorization di Route

### Using Role Middleware
```php
Route::middleware('check.role:admin,manager')->group(function () {
    Route::resource('users', UserController::class);
});
```

### Using Permission Middleware
```php
Route::middleware('check.permission:view_users')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
```

### Multiple Permissions (User needs ALL)
```php
Route::middleware('check.permission:view_users,create_users')->get('/users/create', [UserController::class, 'create']);
```

## 🎨 Authorization di View/Template

### Show if Can
```blade
@can('create_users')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        Create New User
    </a>
@endcan
```

### Show if Cannot
```blade
@cannot('delete_users')
    <p class="alert alert-warning">
        Anda tidak memiliki permission untuk menghapus user
    </p>
@endcannot
```

### Inline Check
```blade
<a href="{{ route('users.edit', $user) }}" 
   @if(!auth()->user()->can('edit_users')) style="display:none;" @endif>
    Edit
</a>
```

### Check Role
```blade
@if(auth()->user()->hasRole('admin'))
    <div class="admin-panel">
        <!-- Admin only content -->
    </div>
@endif
```

### Check Permission
```blade
@if(auth()->user()->hasPermission('delete_users'))
    <button onclick="deleteUser({{ $user->id }})">Delete</button>
@endif
```

## 📋 Available Roles

| Role | Display Name | Permissions |
|------|-------------|-------------|
| admin | Administrator | All |
| manager | Manajer | view_users, create_users, edit_users, view_roles, view_permissions |
| user | Pengguna Biasa | view_users, view_roles, view_permissions |

## 🔐 Available Permissions

### User Permissions
| Permission | Role | Role | Role |
|-----------|------|------|------|
| view_users | ✅ | ✅ | ✅ |
| create_users | ✅ | ✅ | ❌ |
| edit_users | ✅ | ✅ | ❌ |
| delete_users | ✅ | ❌ | ❌ |

### Role Permissions
| Permission | Role | Role | Role |
|-----------|------|------|------|
| view_roles | ✅ | ❌ | ✅ |
| create_roles | ✅ | ❌ | ❌ |
| edit_roles | ✅ | ❌ | ❌ |
| delete_roles | ✅ | ❌ | ❌ |

### Permission Permissions
| Permission | Role | Role | Role |
|-----------|------|------|------|
| view_permissions | ✅ | ❌ | ✅ |
| create_permissions | ✅ | ❌ | ❌ |
| edit_permissions | ✅ | ❌ | ❌ |
| delete_permissions | ✅ | ❌ | ❌ |

## 🔄 Assign Role to User

### In Database Seeder
```php
$user = User::find(1);
$role = Role::where('name', 'admin')->first();
$user->roles()->attach($role);
```

### In Tinker
```bash
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $role = App\Models\Role::where('name', 'admin')->first();
>>> $user->roles()->attach($role);
>>> exit
```

### In Controller
```php
$user->assignRole('admin');
// or
$user->roles()->attach($role->id);
```

## 🔄 Assign Permission to Role

### In Database Seeder
```php
$role = Role::where('name', 'manager')->first();
$permission = Permission::where('name', 'create_users')->first();
$role->permissions()->attach($permission);
```

### In Controller
```php
$role->givePermissionTo('create_users');
// or
$role->permissions()->attach($permission->id);
```

## 🚫 Error Handling

### If Unauthorized
- **Status Code:** 403 Forbidden
- **Default Message:** "Anda tidak memiliki akses ke halaman ini."

### Custom Error Message
```php
// In Model Policy
public function update(User $user, User $model): bool
{
    if (!$user->hasPermission('edit_users')) {
        return response()->view('errors.403', [
            'message' => 'Anda tidak memiliki permission untuk mengedit user ini'
        ], 403);
    }
    return true;
}
```

## 🧪 Testing Authorization

### In Tinker
```php
php artisan tinker

// Check user role
>>> $user = App\Models\User::find(1);
>>> $user->hasRole('admin')
=> true

// Check user permission
>>> $user->hasPermission('view_users')
=> true

// Check if user can do action
>>> auth()->user()->can('edit_users', $user)
=> true

// List user roles
>>> $user->roles()->get()
=> Collection of roles

// List user permissions
>>> $user->roles()->with('permissions')->get()->pluck('permissions').flatten()
=> Collection of permissions
```

## 📝 Common Patterns

### Only Admin Can Access
```php
public function __construct()
{
    $this->middleware(function ($request, $next) {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }
        return $next($request);
    });
}
```

### Admin or Manager Can Access
```php
public function index()
{
    $this->authorize('viewAny', User::class);
    // This checks if user has view_users permission
    // which is assigned to both admin and manager roles
}
```

### Only User's Own Data
```php
public function show(User $user)
{
    $this->authorize('view', $user);
    // And add additional check
    if (auth()->id() !== $user->id && !auth()->user()->hasRole('admin')) {
        abort(403);
    }
}
```

## 🔗 Related Files

- **Policies:** `app/Policies/*Policy.php`
- **Middleware:** `app/Http/Middleware/Check*.php`
- **Models:** `app/Models/User.php`, `Role.php`, `Permission.php`
- **Seeders:** `database/seeders/Permission*.php`, `Role*.php`
- **Documentation:** `AUTHORIZATION.md`, `SETUP_AUTHORIZATION.md`

## ⚡ Tips & Tricks

1. **Always use Policy authorization** - More maintainable than middleware alone
2. **Use `@can` in views** - Don't let UI show buttons user can't use
3. **Cache permissions** - For production, consider caching permission checks
4. **Use descriptive permission names** - Make it clear what each permission does
5. **Create roles with purpose** - Don't create too many roles, keep it simple
6. **Document role responsibilities** - In the role's description field
7. **Test thoroughly** - Test both authorized and unauthorized access
8. **Use gates for complex logic** - For complex authorization, create custom gates

## 🆘 Troubleshooting

### User sees 403 but should have access
1. Check user's roles: `$user->roles()->get()`
2. Check role's permissions: `$role->permissions()->get()`
3. Verify in database tables: `role_user`, `role_permission`

### Middleware not working
1. Check middleware registered in `Kernel.php`
2. Verify route middleware syntax
3. Make sure user is authenticated

### Policy not being called
1. Ensure policy is registered in `AuthServiceProvider`
2. Check model name matches policy
3. Verify method names in policy

---

**Last Updated:** 2024
**Version:** 1.0
