<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['name', 'slug', 'tagline', 'thumbnail', 'is_active'];

    public $casts = [
        'is_active' => 'boolean'
    ];

    function scopeActive($query)
    {
        $query->where('is_active', true);
    }

    public function meals()
    {
        return $this->belongsToMany(Meal::class, MealPackage::class);
    }
}
