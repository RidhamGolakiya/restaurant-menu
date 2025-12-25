<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = file_get_contents(storage_path('countries.json'));
        $countries = json_decode($countries, true)['countries'];
        Country::insert($countries);
    }
}
