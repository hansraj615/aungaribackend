<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = [
        'title_en',
        'title_hi',
        'slug',
        'body_en',
        'body_hi',
        'images'
    ];

    protected $casts = [
        'images' => 'array'
    ];
}
