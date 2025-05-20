<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{


    protected $fillable = [
        'title',
        'slug',
        'body',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
    ];
}
