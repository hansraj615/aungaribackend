<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $table = 'visitor_counts';

    protected $fillable = [
        'total_visits',
        'unique_visitors'
    ];
}
