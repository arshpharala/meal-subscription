<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['province_id', 'name'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
