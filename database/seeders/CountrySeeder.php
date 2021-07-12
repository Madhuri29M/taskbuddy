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

       	Country::updateOrCreate(
            ['country_code' => '+91',
             'name' => 'India',
             'flag' => 'flags/india.png',
             'status' => '1',
            ]
        );
	}

}
