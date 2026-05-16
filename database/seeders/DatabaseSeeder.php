<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run permission and role seeders first
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);

        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.co.id'],
            [
                'name' => 'Admin',
                'password' => Hash::make('tes123'),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role to admin user
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminUser->roles()->sync([$adminRole->id]);
        }

        // Uncomment to create test users
        // \App\Models\User::factory(10)->create();
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

