<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // User Permissions
            ['name' => 'view_user', 'display_name' => 'View Users', 'description' => 'Melihat daftar users'],
            ['name' => 'create_user', 'display_name' => 'Create User', 'description' => 'Membuat user baru'],
            ['name' => 'edit_user', 'display_name' => 'Edit User', 'description' => 'Mengubah data user'],
            ['name' => 'delete_user', 'display_name' => 'Delete User', 'description' => 'Menghapus user'],

            // Role Permissions
            ['name' => 'view_role', 'display_name' => 'View Roles', 'description' => 'Melihat daftar roles'],
            ['name' => 'create_role', 'display_name' => 'Create Role', 'description' => 'Membuat role baru'],
            ['name' => 'edit_role', 'display_name' => 'Edit Role', 'description' => 'Mengubah data role'],
            ['name' => 'delete_role', 'display_name' => 'Delete Role', 'description' => 'Menghapus role'],

            // Permission Permissions
            ['name' => 'view_permission', 'display_name' => 'View Permissions', 'description' => 'Melihat daftar permissions'],
            ['name' => 'create_permission', 'display_name' => 'Create Permission', 'description' => 'Membuat permission baru'],
            ['name' => 'edit_permission', 'display_name' => 'Edit Permission', 'description' => 'Mengubah data permission'],
            ['name' => 'delete_permission', 'display_name' => 'Delete Permission', 'description' => 'Menghapus permission'],

            // Master Penghuni Permissions
            ['name' => 'create_master_penghuni', 'display_name' => 'Create Master Penghuni', 'description' => 'Membuat data rumah baru'],
            ['name' => 'edit_master_penghuni', 'display_name' => 'Edit Master Penghuni', 'description' => 'Mengubah data rumah'],
            ['name' => 'delete_master_penghuni', 'display_name' => 'Delete Master Penghuni', 'description' => 'Menghapus data rumah'],

            // Anggota Umroh Permissions
            ['name' => 'view_anggota', 'display_name' => 'View Anggota Umroh', 'description' => 'Melihat daftar anggota umroh'],
            ['name' => 'create_anggota', 'display_name' => 'Create Anggota Umroh', 'description' => 'Membuat anggota umroh'],
            ['name' => 'edit_anggota', 'display_name' => 'Edit Anggota Umroh', 'description' => 'Mengubah anggota umroh'],
            ['name' => 'delete_anggota', 'display_name' => 'Delete Anggota Umroh', 'description' => 'Menghapus anggota umroh'],

            // Tagihan Permissions
            ['name' => 'view_tagihan', 'display_name' => 'View Tagihan', 'description' => 'Melihat daftar tagihan'],
            ['name' => 'create_tagihan', 'display_name' => 'Create Tagihan', 'description' => 'Membuat dan generate tagihan'],
            ['name' => 'edit_tagihan', 'display_name' => 'Edit Tagihan', 'description' => 'Mengubah tagihan'],
            ['name' => 'delete_tagihan', 'display_name' => 'Delete Tagihan', 'description' => 'Menghapus dan reset tagihan'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full access to the system',
            ]
        );

        $editorRole = Role::firstOrCreate(
            ['name' => 'editor'],
            [
                'display_name' => 'Editor',
                'description' => 'Can manage content and users',
            ]
        );

        $viewerRole = Role::firstOrCreate(
            ['name' => 'viewer'],
            [
                'display_name' => 'Viewer',
                'description' => 'Can only view content',
            ]
        );

        // Assign Permissions to Roles
        // Admin gets all permissions
        $allPermissions = Permission::all()->pluck('id')->toArray();
        $adminRole->permissions()->sync($allPermissions);

        // Editor gets user, role, and permission permissions (except delete)
        $editorPermissions = Permission::whereIn('name', [
            'view_user', 'create_user', 'edit_user',
            'view_role', 'create_role', 'edit_role',
            'view_permission', 'create_permission', 'edit_permission',
        ])->pluck('id')->toArray();
        $editorRole->permissions()->sync($editorPermissions);

        // Viewer only gets view permissions
        $viewerPermissions = Permission::whereIn('name', [
            'view_user', 'view_role', 'view_permission',
        ])->pluck('id')->toArray();
        $viewerRole->permissions()->sync($viewerPermissions);

        // Assign admin role to first admin user if exists
        $adminUser = User::where('email', 'admin@admin.co.id')->first();
        if ($adminUser) {
            $adminUser->roles()->sync([$adminRole->id]);
        }
    }
}
