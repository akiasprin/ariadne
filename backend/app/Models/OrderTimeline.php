<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTimeline extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'state', 'operated_user_id'
    ];

    protected $hidden = [
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'operated_user_id');
    }

}
