<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use App\Models\Catalog\Calorie;
use Illuminate\Auth\Access\Response;

class CaloriePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User|Admin $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User|Admin $user, Calorie $calorie): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User|Admin $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User|Admin $user, Calorie $calorie): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User|Admin $user, Calorie $calorie): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User|Admin $user, Calorie $calorie): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User|Admin $user, Calorie $calorie): bool
    {
        return true;
    }
}
