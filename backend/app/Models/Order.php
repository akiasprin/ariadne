<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $fillable = [
        'status', 'address_id', 'sum', 'express_name', 'express_code',
        'merchant_id', 'customer_id'
    ];

    protected $hidden = [
    ];

    public function goods()
    {
        return $this
            ->belongsToMany('App\Models\Good', 'order_details')
            ->withPivot('quantity');
    }

    public function timelines()
    {
        return $this
            ->hasMany('App\Models\OrderTimeline', 'order_id');
    }

    public function address()
    {
        return $this->belongsTo('App\Models\Address', 'address_id');
    }

    public function merchant()
    {
        return $this->belongsTo('App\Models\User', 'merchant_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\User', 'customer_id');
    }
}
