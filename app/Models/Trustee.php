<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trustee extends Model
{
    protected $fillable = [
        'name',
        'designation',
        'email',
        'phone',
        'address',
        'image', // ✅ Add this line
    ];
}
