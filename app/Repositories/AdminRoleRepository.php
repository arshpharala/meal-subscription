<?php

namespace App\Repositories;

use App\Models\Admin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminRoleRepository
{
    /**
     * Get all roles for an admin (cached).
     */
    public function getForAdmin(Admin $admin): array
    {
        return Cache::remember(
            $this->cacheKey($admin->id),
            now()->addMinutes(10),
            function () use ($admin) {
                return $admin->roles
                    ->pluck('name')
                    ->map(fn ($role) => Str::lower($role))
                    ->toArray();
            }
        );
    }

    /**
     * Clear cached roles for an admin.
     */
    public function clearForAdmin(Admin $admin): void
    {
        Cache::forget($this->cacheKey($admin->id));
    }

    /**
     * Cache key helper.
     */
    protected function cacheKey(int $adminId): string
    {
        return Admin::CACHE_KEY_PREFIX_ROLES . $adminId;
    }
}
