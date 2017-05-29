<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'name', 'phone', 'province', 'city',
        'area', 'street', 'post_code', 'user_id',
    ];

    protected $hidden = [
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function order()
    {
        return $this->hasOne('App\Models\Order', 'buyer_address_id');
    }
}
