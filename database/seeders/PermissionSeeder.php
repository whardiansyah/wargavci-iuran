<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Permissions
            [
                'name' => 'view_users',
                'display_name' => 'Lihat Pengguna',
                'description' => 'Izin untuk melihat daftar pengguna',
            ],
            [
                'name' => 'create_users',
                'display_name' => 'Buat Pengguna',
                'description' => 'Izin untuk membuat pengguna baru',
            ],
            [
                'name' => 'edit_users',
                'display_name' => 'Edit Pengguna',
                'description' => 'Izin untuk mengedit pengguna',
            ],
            [
                'name' => 'delete_users',
                'display_name' => 'Hapus Pengguna',
                'description' => 'Izin untuk menghapus pengguna',
            ],
            // Role Permissions
            [
                'name' => 'view_roles',
                'display_name' => 'Lihat Peran',
                'description' => 'Izin untuk melihat daftar peran',
            ],
            [
                'name' => 'create_roles',
                'display_name' => 'Buat Peran',
                'description' => 'Izin untuk membuat peran baru',
            ],
            [
                'name' => 'edit_roles',
                'display_name' => 'Edit Peran',
                'description' => 'Izin untuk mengedit peran',
            ],
            [
                'name' => 'delete_roles',
                'display_name' => 'Hapus Peran',
                'description' => 'Izin untuk menghapus peran',
            ],
            // Permission Permissions
            [
                'name' => 'view_permissions',
                'display_name' => 'Lihat Izin',
                'description' => 'Izin untuk melihat daftar izin',
            ],
            [
                'name' => 'create_permissions',
                'display_name' => 'Buat Izin',
                'description' => 'Izin untuk membuat izin baru',
            ],
            [
                'name' => 'edit_permissions',
                'display_name' => 'Edit Izin',
                'description' => 'Izin untuk mengedit izin',
            ],
            [
                'name' => 'delete_permissions',
                'display_name' => 'Hapus Izin',
                'description' => 'Izin untuk menghapus izin',
            ],
            // Master Config Permissions
            [
                'name' => 'view_master_configs',
                'display_name' => 'Lihat Master Config',
                'description' => 'Izin untuk melihat daftar konfigurasi master',
            ],
            [
                'name' => 'create_master_configs',
                'display_name' => 'Buat Master Config',
                'description' => 'Izin untuk membuat konfigurasi master baru',
            ],
            [
                'name' => 'edit_master_configs',
                'display_name' => 'Edit Master Config',
                'description' => 'Izin untuk mengedit konfigurasi master',
            ],
            [
                'name' => 'delete_master_configs',
                'display_name' => 'Hapus Master Config',
                'description' => 'Izin untuk menghapus konfigurasi master',
            ],
            [
                'name' => 'view_anggota',
                'display_name' => 'Lihat Anggota Umroh',
                'description' => 'Izin untuk melihat daftar anggota umroh',
            ],
            [
                'name' => 'create_anggota',
                'display_name' => 'Buat Anggota Umroh',
                'description' => 'Izin untuk membuat data anggota umroh baru',
            ],
            [
                'name' => 'edit_anggota',
                'display_name' => 'Edit Anggota Umroh',
                'description' => 'Izin untuk mengedit data anggota umroh',
            ],
            [
                'name' => 'delete_anggota',
                'display_name' => 'Hapus Anggota Umroh',
                'description' => 'Izin untuk menghapus data anggota umroh',
            ],
            [
                'name' => 'view_pencatatan_air',
                'display_name' => 'Lihat Pencatatan Air',
                'description' => 'Izin untuk melihat daftar pencatatan air',
            ],
            [
                'name' => 'create_pencatatan_air',
                'display_name' => 'Buat Pencatatan Air',
                'description' => 'Izin untuk membuat data pencatatan air baru',
            ],
            [
                'name' => 'edit_pencatatan_air',
                'display_name' => 'Edit Pencatatan Air',
                'description' => 'Izin untuk mengedit data pencatatan air',
            ],
            [
                'name' => 'delete_pencatatan_air',
                'display_name' => 'Hapus Pencatatan Air',
                'description' => 'Izin untuk menghapus data pencatatan air',
            ],
            [
                'name' => 'export_pencatatan_air',
                'display_name' => 'Export Pencatatan Air',
                'description' => 'Izin untuk mengexport data pencatatan air ke Excel',
            ],
            [
                'name' => 'view_tagihan',
                'display_name' => 'Lihat Tagihan',
                'description' => 'Izin untuk melihat daftar tagihan',
            ],
            [
                'name' => 'create_tagihan',
                'display_name' => 'Buat Tagihan',
                'description' => 'Izin untuk membuat dan generate tagihan',
            ],
            [
                'name' => 'edit_tagihan',
                'display_name' => 'Edit Tagihan',
                'description' => 'Izin untuk mengedit data tagihan',
            ],
            [
                'name' => 'delete_tagihan',
                'display_name' => 'Hapus Tagihan',
                'description' => 'Izin untuk menghapus dan reset tagihan',
            ],
            [
                'name' => 'view_transaksi_kas',
                'display_name' => 'Lihat Transaksi Kas',
                'description' => 'Izin untuk melihat daftar transaksi kas',
            ],
            [
                'name' => 'create_transaksi_kas',
                'display_name' => 'Buat Transaksi Kas',
                'description' => 'Izin untuk membuat data transaksi kas baru',
            ],
            [
                'name' => 'edit_transaksi_kas',
                'display_name' => 'Edit Transaksi Kas',
                'description' => 'Izin untuk mengedit data transaksi kas',
            ],
            [
                'name' => 'delete_transaksi_kas',
                'display_name' => 'Hapus Transaksi Kas',
                'description' => 'Izin untuk menghapus data transaksi kas',
            ],
            [
                'name' => 'export_transaksi_kas',
                'display_name' => 'Export Transaksi Kas',
                'description' => 'Izin untuk mengexport data transaksi kas ke Excel',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'description' => $permission['description'],
                ]
            );
        }
    }
}
