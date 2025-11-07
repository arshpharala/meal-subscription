<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Module;

class ModulePolicy
{
    /**
     * Can view module list.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->has_permission('Module', 'View List');
    }

    /**
     * Can view a specific module.
     */
    public function view(Admin $admin, Module $module): bool
    {
        return $admin->has_permission('Module', 'View');
    }

    /**
     * Can create modules.
     */
    public function create(Admin $admin): bool
    {
        return $admin->has_permission('Module', 'Create');
    }

    /**
     * Can update a module.
     */
    public function update(Admin $admin, Module $module): bool
    {
        return $admin->has_permission('Module', 'Update');
    }

    /**
     * Can delete a module.
     */
    public function delete(Admin $admin, Module $module): bool
    {
        return $admin->has_permission('Module', 'Delete');
    }
}
