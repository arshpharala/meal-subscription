<?php

namespace App\Repositories;

use App\Models\Admin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminPermissionRepository
{
    /**
     * Get all permissions for a given admin (cached).
     */
    public function getForAdmin(Admin $admin): array
    {
        return Cache::remember(
            $this->cacheKey($admin->id),
            now()->addMinutes(10),
            function () use ($admin) {
                return $admin->roles
                    ->load('permissions.module')
                    ->pluck('permissions')
                    ->collapse()
                    ->map(fn($perm) => [
                        'module' => Str::lower($perm->module->name),
                        'name'   => Str::lower($perm->name),
                    ])
                    ->toArray();
            }
        );
    }

    /**
     * Clear cached permissions for a admin.
     */
    public function clearForadmin(Admin $admin): void
    {
        Cache::forget($this->cacheKey($admin->id));
    }

    /**
     * Helper to build cache key.
     */
    protected function cacheKey(int $adminId): string
    {
        return Admin::CACHE_KEY_PREFIX_PERMISSIONS . $adminId;
    }
}
