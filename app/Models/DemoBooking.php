<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'restaurant_name',
        'email',
        'phone',
        'message',
        'is_contacted',
    ];

    protected $casts = [
        'is_contacted' => 'boolean',
    ];
}
