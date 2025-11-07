<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Super  Admin',
            'password' => bcrypt('Pa$$w0rd'),
            'is_active' => 1,
            'email_verified_at' => now()
        ]);

        $superAdminRole = Role::firstOrCreate(
            ['name' => 'SuperAdmin'],
            ['is_active' => true]
        );

        $admin->roles()->syncWithoutDetaching([$superAdminRole->id]);
    }
}
