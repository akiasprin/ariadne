<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'sign',
        'curr_login_at', 'last_login_at'
    ];

    protected $hidden = [
        'password', 'curr_login_at'
    ];

    public function findForPassport($username)
    {
        return $this->where('phone', $username)->first();
    }

    public function goods()
    {
        return $this->hasMany('App\Models\Good', 'user_id')
            ->select(
            [
                'id', 'title', 'desc', 'cover', 'price', 'total', 'unit', 'province', 'city',
                'state', 'views', 'sales', 'quality', 'purchased_at', 'created_at', 'updated_at',
                'user_id'
            ]);
    }

    public function addresses()
    {
        return $this->hasMany('App\Models\Address', 'user_id');
    }

    public function cart()
    {
        return $this->belongsToMany('App\Models\Good', 'carts');
    }

    public function shopping_orders()
    {
        return $this->hasMany('App\Models\Order', 'customer_id');
    }

    public function sales_orders()
    {
        return $this->hasMany('App\Models\Order', 'merchant_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'user_id');
    }
}