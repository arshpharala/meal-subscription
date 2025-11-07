<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Calorie extends Model
{
    use HasUuids;

    protected $fillable = [
        'label',
        'min_kcal',
        'max_kcal',
        'is_active'
    ];

    public $casts = [
        'is_active' => 'boolean'
    ];

    function scopeActive($query)
    {
        $query->where('is_active', true);
    }
}
