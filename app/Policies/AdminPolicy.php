<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class AdminPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin|User $user): bool
    {
        return $user->has_permission('Admin', 'View List');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin|User $user, Admin $admin): bool
    {
        return $user->has_permission('Admin', 'View');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin|User $user): bool
    {
        return $user->has_permission('Admin', 'Create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin|User $user, Admin $admin): bool
    {
        return $user->has_permission('Admin', 'Update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin|User $user, Admin $admin): bool
    {

        return $user->has_permission('Admin', 'Delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Admin|User $user, Admin $admin): bool
    {

        return $user->has_permission('Admin', 'Restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Admin|User $user, Admin $admin): bool
    {
        return $user->has_permission('Admin', 'Delete Permanent');
    }
}
