<?php

namespace App\Repositories;

use App\Models\Module;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class ModuleRepository extends BaseRepository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return Module::class;
    }

    /**
     * Cached list of modules (name → id).
     */
    public function allCached(): array
    {
        return Cache::remember(
            Module::CACHE_KEY,
            now()->addMinutes(30),
            fn () => Module::pluck('id', 'name')
                ->mapWithKeys(fn ($id, $name) => [Str::lower($name) => $id])
                ->toArray()
        );
    }

    /**
     * Override create to refresh cache.
     */
    public function create(array $attributes)
    {
        $object = parent::create($attributes);

        $this->clearCache();

        return $object->refresh();
    }

    /**
     * Override update to refresh cache.
     */
    public function update(array $attributes, $id)
    {
        $object = parent::update($attributes, $id);

        $this->clearCache();

        return $object->refresh();
    }

    /**
     * Override delete to refresh cache.
     */
    public function delete($id)
    {
        $deleted = parent::delete($id);

        $this->clearCache();

        return $deleted;
    }

    /**
     * Forget cached modules.
     */
    public function clearCache(): void
    {
        Cache::forget(Module::CACHE_KEY);
    }
}
