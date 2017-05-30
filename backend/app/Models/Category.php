<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'slug', 'parent_id'
    ];

    public function goods()
    {
        return $this->belongsToMany('App\Models\Good', 'category_relationships')->select(
            [
                'id', 'title', 'desc', 'cover', 'price', 'total', 'unit', 'province', 'city',
                'state', 'views', 'sales', 'quality', 'purchased_at', 'created_at', 'updated_at',
                'user_id'
            ]
        );
    }

}
