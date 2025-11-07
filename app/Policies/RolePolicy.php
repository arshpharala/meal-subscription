<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Role;

class RolePolicy
{
    /**
     * Can view list of roles.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->has_permission('Role', 'View List');
    }

    /**
     * Can view a specific role.
     */
    public function view(Admin $admin, Role $role): bool
    {
        return $admin->has_permission('Role', 'View');
    }

    /**
     * Can create a new role.
     */
    public function create(Admin $admin): bool
    {
        return $admin->has_permission('Role', 'Create');
    }

    /**
     * Can update a role.
     */
    public function update(Admin $admin, Role $role): bool
    {
        return $admin->has_permission('Role', 'Update');
    }

    /**
     * Can delete a role.
     */
    public function delete(Admin $admin, Role $role): bool
    {
        return $admin->has_permission('Role', 'Delete');
    }
}
