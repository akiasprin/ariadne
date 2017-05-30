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
            ->withPivot('quantity')
            ->select(
                [
                    'id', 'title', 'desc', 'cover', 'price', 'total', 'unit', 'province', 'city',
                    'state', 'views', 'sales', 'quality', 'purchased_at', 'created_at', 'updated_at',
                    'user_id'
                ]);
    }

    public function timelines()
    {
        return $this->hasMany('App\Models\OrderTimeline', 'order_id');
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
