<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Restaurant Admin',
            'email' => 'restaurant@gmail.com',
            'restaurant_id' => null,
            'status' => \App\Models\User::ACTIVE,
            'password' => bcrypt('123456'),
        ]);
        $restaurant = Restaurant::create([
            'name' => 'Restaurant Admin',
            'phone' => '9652458965',
            'address' => '123 Main St',
            'address_2' => '123 Main St',
            'city' => 'Surat',
            'state' => 'Gujarat',
            'country_id' => 1,
            'zip_code' => '12345',
        ]);

        $user->restaurant_id = $restaurant->id;
        $user->save();

        $user->assignRole('restaurant');
    }
}
