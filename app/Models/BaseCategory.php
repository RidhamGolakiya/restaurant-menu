<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseCategory extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'slug',
        'sort_order',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuCategories()
    {
        return $this->hasMany(MenuCategory::class, 'base_category_id');
    }
}
