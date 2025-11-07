<?php

namespace App\Http\Controllers;

use App\Models\CMS\Area;
use App\Models\CMS\City;
use App\Models\Catalog\Meal;
use App\Models\Catalog\Package;
use App\Models\Catalog\MealPackage;
use App\Models\Catalog\MealPackagePrice;
use App\Models\User;

class Controller
{
    public function getCities($provinceId)
    {
        $cities = City::where('province_id', $provinceId)->get();
        return response()->json($cities);
    }

    public function getAreas($cityId)
    {
        $areas = Area::where('city_id', $cityId)->get();
        return response()->json($areas);
    }


}
