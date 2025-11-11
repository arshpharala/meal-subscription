<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calorie extends Model
{
    use HasUuids, SoftDeletes;

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
