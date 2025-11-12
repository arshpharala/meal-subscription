<?php

namespace App\Models;

use App\Models\CMS\Area;
use App\Models\CMS\City;
use App\Models\CMS\Country;
use App\Models\CMS\Province;
use App\Models\Sales\Subscription;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['user_id', 'type', 'name', 'phone', 'country_id', 'province_id', 'city_id', 'area_id', 'address', 'landmark'];


    public static function getTypes()
    {
        return  collect([
            (object)['key' => 'home', 'name' => 'Home'],
            (object)['key' => 'work', 'name' => 'Work'],
            (object)['key' => 'other', 'name' => 'Other'],
        ]);
    }

    function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function render(bool $plain = false): mixed
    {
        if ($plain) {
            return "{$this->address},
                    {$this->area->name},
                    {$this->area->landmark}
                    {$this->city->name},
                    {$this->province->name},
                    {$this->country->name}";
        }

        return "<div>
                    {$this->address},
                    {$this->area->name},
                    {$this->area->landmark}
                    <br />
                    {$this->city->name},
                    {$this->province->name},
                    {$this->country->name}
        </div>";
    }
}
