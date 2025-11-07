<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create SuperAdmin role if it doesn't exist
        $superAdmin = Role::firstOrCreate(
            ['name' => 'SuperAdmin'],
            ['is_active' => true]
        );

        // Attach all permissions to SuperAdmin
        $allPermissions = Permission::all()->pluck('id')->toArray();
        $superAdmin->permissions()->sync($allPermissions);
    }
}
