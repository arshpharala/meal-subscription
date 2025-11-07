<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ExtraCharge extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'type', 'amount', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
