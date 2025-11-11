<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class MealPackage extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['meal_id', 'package_id', 'code', 'is_active', 'stripe_product_id'];

    public $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        $query->where('is_active', 1);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function prices()
    {
        return $this->hasMany(MealPackagePrice::class);
    }
}
