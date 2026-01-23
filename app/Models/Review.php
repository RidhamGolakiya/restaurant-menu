<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'author_name',
        'author_url',
        'profile_photo_url',
        'rating',
        'text',
        'relative_time_description',
        'time',
        'is_visible',
        'google_review_id',
    ];

    protected $casts = [
        'time' => 'datetime',
        'is_visible' => 'boolean',
        'rating' => 'integer',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
