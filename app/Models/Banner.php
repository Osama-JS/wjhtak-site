<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'banners';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'image_path',
        'link',
        'trip_id',
        'sort_order',
        'active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get localized title based on current locale.
     */
    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->title_ar ?? '') : ($this->title_en ?? '');
    }

    /**
     * Get localized description based on current locale.
     */
    public function getDescriptionAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->description_ar ?? '') : ($this->description_en ?? '');
    }

    /**
     * Get image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/banners/default.jpg');
    }

    /**
     * Get associated trip (if any).
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Scope a query to only include active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
