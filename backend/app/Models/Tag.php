<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'slug'
    ];

    public function goods()
    {
        return $this->belongsToMany('App\Models\Goods', 'tags');
    }

}
