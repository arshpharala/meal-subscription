<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Permission;

class PermissionPolicy
{
    /**
     * Can view list of permissions.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->has_permission('Permission', 'View List');
    }

    /**
     * Can view a specific permission.
     */
    public function view(Admin $admin, Permission $permission): bool
    {
        return $admin->has_permission('Permission', 'View');
    }

    /**
     * Can create a new permission.
     */
    public function create(Admin $admin): bool
    {
        return $admin->has_permission('Permission', 'Create');
    }

    /**
     * Can update a permission.
     */
    public function update(Admin $admin, Permission $permission): bool
    {
        return $admin->has_permission('Permission', 'Update');
    }

    /**
     * Can delete a permission.
     */
    public function delete(Admin $admin, Permission $permission): bool
    {
        return $admin->has_permission('Permission', 'Delete');
    }
}
