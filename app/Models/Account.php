<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Account extends Authenticatable
{
    protected $table = 'accounts';

    protected $fillable = [
        'name',
        'email',
        'password',
        'wrong_attempts',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
