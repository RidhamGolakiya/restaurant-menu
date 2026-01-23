<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestaurantTimingSlot;
use Illuminate\Support\Facades\Auth;

class RestaurantTimingController extends Controller
{
    public function save(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $timings = $request->input('timings', []);
    
        $allDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    
        foreach ($allDays as $day) {
            RestaurantTimingSlot::where('restaurant_id', $restaurantId)
                ->where('day_name', $day)
                ->delete();
    
            if (isset($timings[$day])) {
                foreach ($timings[$day] as $slot) {
                    if (!empty($slot['open_time']) && !empty($slot['close_time'])) {
                        RestaurantTimingSlot::create([
                            'restaurant_id' => $restaurantId,
                            'day_name' => $day,
                            'open_time' => $slot['open_time'],
                            'close_time' => $slot['close_time'],
                        ]);
                    }
                }
            }
        }
    
        return response()->json(['status' => 'success']);
    }
    
    
}
