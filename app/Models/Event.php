<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
  use HasFactory;

  protected $fillable = [
    'name_en',
    'name_hi',
    'description_en',
    'description_hi',
    'start_date',
    'end_date',
    'show_in_banner',
    'image',
  ];
}
