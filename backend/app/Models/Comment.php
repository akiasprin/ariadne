<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'content', 'good_id', 'user_id'
    ];

    public function good()
    {
        return $this
            ->belongsTo('App\Models\Good', 'good_id');
    }

    public function user()
    {
        return $this
            ->belongsTo('App\Models\User', 'user_id');
    }

}
