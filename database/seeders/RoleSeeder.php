<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Role with all permissions
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Administrator memiliki akses penuh ke semua fitur aplikasi',
            ]
        );

        // Attach all permissions to admin role
        $permissions = Permission::all();
        $adminRole->permissions()->sync($permissions->pluck('id')->toArray());

        // Create User Role with limited permissions
        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'Pengguna Biasa',
                'description' => 'Pengguna biasa dengan akses terbatas',
            ]
        );

        // Attach limited permissions to user role (view only)
        $userPermissions = Permission::whereIn('name', [
            'view_users',
            'view_roles',
            'view_permissions',
            'view_master_configs',
            'view_anggota',
            'view_pencatatan_air',
            'export_pencatatan_air',
            'view_tagihan',
            'view_transaksi_kas',
        ])->pluck('id')->toArray();
        $userRole->permissions()->sync($userPermissions);

        // Create Manager Role with moderate permissions
        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manajer',
                'description' => 'Manajer dapat mengelola pengguna dan peran',
            ]
        );

        // Attach moderate permissions to manager role
        $managerPermissions = Permission::whereIn('name', [
            'view_users',
            'create_users',
            'edit_users',
            'view_roles',
            'view_permissions',
            'view_master_configs',
            'create_master_configs',
            'edit_master_configs',
            'delete_master_configs',
            'view_anggota',
            'create_anggota',
            'edit_anggota',
            'delete_anggota',
            'view_pencatatan_air',
            'create_pencatatan_air',
            'edit_pencatatan_air',
            'delete_pencatatan_air',
            'export_pencatatan_air',
            'view_tagihan',
            'create_tagihan',
            'edit_tagihan',
            'delete_tagihan',
            'view_transaksi_kas',
            'create_transaksi_kas',
            'edit_transaksi_kas',
            'delete_transaksi_kas',
            'export_transaksi_kas',
        ])->pluck('id')->toArray();
        $managerRole->permissions()->sync($managerPermissions);
    }
}
