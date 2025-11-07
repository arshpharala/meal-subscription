<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = ['country_id', 'name'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
