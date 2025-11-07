<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Repositories\ModuleRepository;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\AdminRoleRepository;
use App\Repositories\AdminPermissionRepository;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Admin extends Authenticatable
{
    public const CACHE_KEY_PREFIX_PERMISSIONS   = 'admin_permissions:';
    public const CACHE_KEY_PREFIX_ROLES         = 'admin_roles:';

    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_roles');
    }


    /**
     * Check if the admin has one or more roles.
     *
     * @param  string|array  $roleNames  Role name(s) to check (case-insensitive).
     * @return bool  True if admin has at least one of the given roles.
     */
    public function has_role(string|array $roleNames): bool
    {
        $roleNames = is_array($roleNames)
            ? array_map(fn($r) => Str::lower(trim($r)), $roleNames)
            : [Str::lower(trim($roleNames))];

        $roles = app(AdminRoleRepository::class)->getForAdmin($this);

        return ! empty(array_intersect($roles, $roleNames));
    }

    /**
     * Check if the admin has one or more permissions for a module.
     *
     * @param  string        $moduleName  Module name to check (case-insensitive).
     * @param  string|array  $access      Permission name(s) to check (case-insensitive).
     * @return bool  True if admin has at least one of the given permissions for the module.
     */
    public function has_permission(string $moduleName, string|array $access): bool
    {
        $moduleName = Str::lower(trim($moduleName));

        $accessList = is_array($access)
            ? array_map(fn($a) => Str::lower(trim($a)), $access)
            : [Str::lower(trim($access))];

        $permissions = app(AdminPermissionRepository::class)->getForAdmin($this);
        $modules     = app(ModuleRepository::class)->allCached();

        if (! isset($modules[$moduleName])) {
            return false;
        }

        foreach ($permissions as $perm) {
            if ($perm['module'] === $moduleName && in_array($perm['name'], $accessList, true)) {
                return true;
            }
        }

        return false;
    }
}
