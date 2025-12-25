<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnsubscribedUser extends Model
{
    protected $table = 'unsubscribed_users';

    protected $fillable = [
        'email',
    ];
}
