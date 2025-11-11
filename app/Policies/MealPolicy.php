<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use App\Models\Catalog\Meal;
use Illuminate\Auth\Access\Response;

class MealPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin|User $user): bool
    {
        return $user->has_permission('Meal', 'View List');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin|User $user, Meal $meal): bool
    {
        return $user->has_permission('Meal', 'View List');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin|User $user): bool
    {
        return $user->has_permission('Meal', 'Create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin|User $user, Meal $meal): bool
    {
        return $user->has_permission('Meal', 'Update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin|User $user, Meal $meal): bool
    {
        return $user->has_permission('Meal', 'Delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Admin|User $user, Meal $meal): bool
    {
        return $user->has_permission('Meal', 'Restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Admin|User $user, Meal $meal): bool
    {
        return $user->has_permission('Meal', 'Restore');
    }
}
