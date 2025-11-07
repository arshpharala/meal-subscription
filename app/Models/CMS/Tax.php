<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = ['label', 'percentage', 'is_active', 'stripe_id'];

    public function country()
    {
        return $this->hasMany(Country::class);
    }
}
