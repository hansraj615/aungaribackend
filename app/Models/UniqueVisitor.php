<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniqueVisitor extends Model
{
  protected $fillable = [
    'ip_address',
    'first_visit_at',
    'last_visit_at'
  ];

  protected $casts = [
    'first_visit_at' => 'datetime',
    'last_visit_at' => 'datetime'
  ];
}
