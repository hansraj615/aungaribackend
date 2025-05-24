<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
    protected $fillable = [
        'title_en',
        'title_hi',
        'description_en',
        'description_hi',
        'occasion_id',
        'event_date',
        'is_featured',
        'display_order',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function occasion(): BelongsTo
    {
        return $this->belongsTo(Occasion::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class)->orderBy('display_order');
    }

    // Get the first image as the cover image
    public function getCoverImageAttribute(): ?string
    {
        $firstImage = $this->images()->first();
        return $firstImage ? $firstImage->image_url : null;
    }

    // Accessor for getting combined title
    public function getTitleAttribute()
    {
        return $this->title_en;
    }

    // Accessor for getting combined caption
    public function getCaptionAttribute()
    {
        return $this->description_en;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($gallery) {
            // Delete all associated images when the gallery is deleted
            $gallery->images->each->delete();
        });
    }
}
