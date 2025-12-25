<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantDay extends Model
{
    protected $table = 'restaurant_days';

    protected $fillable = ['restaurant_id', 'day_name', 'is_active'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function timingSlots()
    {
        return $this->hasMany(RestaurantTimingSlot::class, 'restaurant_day_id');
    }
}
