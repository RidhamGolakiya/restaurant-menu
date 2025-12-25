<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantDay;
use App\Models\RestaurantTimingSlot;
use Illuminate\Database\Seeder;

class DemoReNewSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $createdDays = [];

        $restaurants = Restaurant::all();
        foreach ($restaurants as $restaurant) {
            $createdDays = [];
            foreach ($days as $dayName) {
                $createdDays[] = RestaurantDay::firstOrCreate([
                    'day_name' => $dayName,
                    'restaurant_id' => $restaurant->id,
                    'is_active' => true,
                ]);
            }

            foreach ($createdDays as $day) {
                RestaurantTimingSlot::where('day_name', $day->day_name)
                    ->where('restaurant_id', $restaurant->id)
                    ->update(['restaurant_day_id' => $day->id]);
            }
        }
    }
}
