<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTiming extends Model
{
    protected $table = 'restaurant_timings';
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


}
