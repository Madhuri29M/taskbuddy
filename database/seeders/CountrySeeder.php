<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

       	$countries = json_decode(file_get_contents(__DIR__ . '/data/countries.json'), true);
        // echo "<pre>";print_r($countries);exit;
        foreach ($countries as $countryId => $country){
            $createArray = [];
            $createArray['name']         = $country['name'];
            $createArray['country_code'] = '+'.$country['calling_code'];
            $createArray['flag']         = "https://www.countryflags.io/".$country['iso_3166_2']."/flat/64.png";
            $createArray['status']       = '0';

            Country::create($createArray);
        }
	}

}
