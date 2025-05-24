<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Occasion extends Model
{
    protected $fillable = [
        'name_en',
        'name_hi',
        'description_en',
        'description_hi',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    // Add accessor for name attribute
    public function getNameAttribute()
    {
        return $this->name_en;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($occasion) {
            if (empty($occasion->slug)) {
                $occasion->slug = Str::slug($occasion->name_en);
            }
        });

        static::updating(function ($occasion) {
            if ($occasion->isDirty('name_en') && !$occasion->isDirty('slug')) {
                $occasion->slug = Str::slug($occasion->name_en);
            }
        });
    }

    // Accessor for getting combined description
    public function getDescriptionAttribute()
    {
        return $this->description_en;
    }
}
