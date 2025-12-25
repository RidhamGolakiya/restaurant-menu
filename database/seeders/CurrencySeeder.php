<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = file_get_contents(storage_path('currencies.json'));
        $currencies = json_decode($currencies, true)['currencies'];
       
        Currency::insert($currencies);
    }
    
}
