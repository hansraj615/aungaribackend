<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    protected $casts = [
        'hero_section' => 'array',
        'about_section' => 'array',
        'dynamic_sections' => 'array'
    ];

    protected $fillable = [
        'hero_section',
        'about_section',
        'dynamic_sections',
        'show_read_more',
        'read_more_char_limit'
    ];
}
