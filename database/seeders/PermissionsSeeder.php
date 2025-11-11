<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            // 🔹 User Management
            'Admin'       => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            'Role'        => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            'Permission'  => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            'Module'      => ['View List', 'Create', 'Update', 'Delete', 'Restore'],

            // 🔹 Catalog
            'Meal'     => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            'Package'    => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            'Calorie'       => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'Attribute'   => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'Coupon'      => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'Vendor'      => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'Offer'       => ['View List', 'Create', 'Update', 'Delete', 'Restore'],

            // // 🔹 Inventory
            // 'InventorySource' => ['View List', 'Create', 'Update', 'Delete', 'Restore'],

            // // 🔹 CMS
            // 'Page'        => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'Banner'      => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'News'        => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'Testimonial' => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'Locale'      => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'Currency'    => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'Country'     => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'Tag'         => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
            // 'Email'       => ['View List', 'Create', 'Update', 'Delete', 'Restore'],
        ];

        foreach ($modules as $moduleName => $permissions) {
            // Create or fetch module
            $module = Module::firstOrCreate(['name' => $moduleName]);

            foreach ($permissions as $perm) {
                Permission::firstOrCreate([
                    'module_id' => $module->id,
                    'name'      => $perm,
                ]);
            }
        }
    }
}
