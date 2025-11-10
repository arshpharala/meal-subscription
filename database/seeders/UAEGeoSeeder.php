<?php

namespace Database\Seeders;

use App\Models\CMS\Tax;
use App\Models\CMS\Area;
use App\Models\CMS\City;
use App\Models\CMS\Locale;
use App\Models\CMS\Country;
use App\Models\CMS\Currency;
use App\Models\CMS\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UAEGeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {

            Locale::updateOrCreate(['code' => 'en'], [
                'name' => 'English',
                'direction' => 'ltr'
            ]);


            $tax = Tax::firstOrCreate(['label' => 'VAT'], ['percentage' => 5, 'is_active' => 1]);

            $currency = Currency::firstOrCreate(
                ['code' => 'AED'],
                [
                    'name' => 'United Arab Emirates Dirham',
                    'symbol' => 'AED',
                    'decimal' => 2,
                    'group_separator' => ',',
                    'decimal_separator' => '.',
                    'currency_position' => 'Left',
                    'symbol_html' => '',
                    'is_default' => 1,
                    'exchange_rate' => '0'
                ]
            );

            $country = Country::firstOrCreate(
                ['code' => 'ae'],
                [
                    'name' => 'United Arab Emirates',
                    'currency_id' => $currency->id,
                    'tax_id' => $tax->id,
                    'icon' => 'flag-uae.png'
                ]
            );

            $data = [
                'Abu Dhabi' => [
                    'Abu Dhabi' => ['Al Maryah Island', 'Al Raha', 'Al Khalidiyah', 'Al Khalifa Street'],
                    'Al Ain' => ['Al Jimi', 'Al Mutared', 'Al Hili'],
                ],
                'Dubai' => [
                    'Dubai' => ['Deira', 'Bur Dubai', 'Marina', 'Jumeirah', 'Downtown'],
                    'Hatta' => ['Hatta Village', 'Hatta Wadi Hub'],
                    'Jebel Ali' => ['JAFZA', 'Jebel Ali Village'],
                ],
                'Sharjah' => [
                    'Sharjah' => ['Al Majaz', 'Al Nahda', 'Al Khan']
                ],
                'Ajman' => [
                    'Ajman' => ['Ajman Corniche', 'Al Nuaimiya']
                ],
                'Umm Al Quwain' => [
                    'Umm Al Quwain' => ['UAQ Free Zone', 'Sinaiya']
                ],
                'Ras Al Khaimah' => [
                    'Ras Al Khaimah' => ['Al Nakheel', 'Al Hamra'],
                    'Dibba Al Hisn' => ['Dibba Corniche']
                ],
                'Fujairah' => [
                    'Fujairah' => ['Fujairah City Centre', 'Masafi', 'Khor Fakkan']
                ],
            ];

            foreach ($data as $provinceName => $cities) {
                $province = Province::firstOrCreate(
                    [
                        'country_id' => $country->id,
                        'name' => $provinceName
                    ]
                );

                foreach ($cities as $cityName => $areas) {
                    $city = City::firstOrCreate(
                        [
                            'province_id' => $province->id,
                            'name' => $cityName
                        ]
                    );

                    foreach ($areas as $areaName) {
                        Area::firstOrCreate(
                            [
                                'city_id' => $city->id,
                                'name' => $areaName
                            ]
                        );
                    }
                }
            }
        });
    }
}
