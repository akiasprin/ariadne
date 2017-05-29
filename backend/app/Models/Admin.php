<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'mail', 'username', 'password', 'role',
        'last_login_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}