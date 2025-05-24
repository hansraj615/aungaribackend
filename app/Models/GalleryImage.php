<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GalleryImage extends Model
{
    protected $fillable = [
        'gallery_id',
        'image_path',
        'caption_en',
        'caption_hi',
        'display_order',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($galleryImage) {
            // Delete the image file when the gallery image record is deleted
            Storage::delete($galleryImage->image_path);
        });
    }
}
