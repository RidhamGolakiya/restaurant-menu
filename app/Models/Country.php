<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $table = 'countries';

    public $fillable = [
        'name',
        'short_code',
        'phone_code',
    ];
}
