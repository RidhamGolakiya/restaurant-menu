<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTimingSlot extends Model
{
    protected $fillable = [
        'restaurant_id',
        'day_name',
        'open_time',
        'close_time',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function restaurantDay()
    {
        return $this->belongsTo(RestaurantDay::class, 'restaurant_day_id');
    }
}
