<?php

namespace App\Models\Catalog;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Meal extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'slug', 'position', 'tagline', 'is_active', 'sample_menu_file', 'starting_price'];

    public $casts = [
        'is_active' => 'boolean'
    ];

    function scopeActive($query)
    {
        $query->where('is_active', true);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, MealPackage::class);
    }

    function mealPackages()
    {
        return $this->hasMany(MealPackage::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }


    public function refreshStartingPrice(): void
    {
        $min = MealPackagePrice::query()
            ->whereIn('meal_package_id', $this->packages()->pluck('id'))
            ->min('price');
        $this->starting_price = $min ?: null;
        $this->saveQuietly();
    }

    public function scopeWithJoins($query)
    {
        return $query
            ->leftJoin('attachments as main_attachment', function ($join) {
                $join->on('main_attachment.attachable_id', '=', 'meals.id')
                    ->where('main_attachment.attachable_type', Meal::class)
                    ->whereRaw('main_attachment.id = (
                    SELECT a.id FROM attachments a
                    WHERE a.attachable_id = meals.id
                      AND a.attachable_type = "' . addslashes(Meal::class) . '"
                    ORDER BY a.created_at ASC LIMIT 1
                )');
            });
    }

    public function scopeWithSelection($query)
    {
        return $query->select([
            'meals.id as id',
            'meals.slug',
            'meals.name',
            'meals.tagline',
            'meals.starting_price',
            'meals.position',
            'meals.is_active',
            'meals.created_at',
            'main_attachment.file_path as thumbnail_file_path',
            'main_attachment.file_name as thumbnail_file_name',
        ]);
    }
}
