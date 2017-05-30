<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    protected $fillable = [
        'state', 'quality', 'title', 'content', 'desc', 'cover',
        'price', 'total', 'unit', 'province', 'city',
        'user_id', 'purchased_at', 'views'
    ];

    protected $hidden = [
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'category_relationships');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'tag_relationships');
    }

    public function orders()
    {
        return $this
            ->belongsToMany('App\Models\Order', 'order_details')
            ->withPivot('quantity');
    }

    public function carts()
    {
        return $this
            ->belongsToMany('App\Models\User', 'carts')
            ->withPivot('quantity');
    }

    public function comments()
    {
        return $this
            ->hasMany('App\Models\Comment', 'good_id');
    }

}
