<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
    ];

    /**
     * Get localized name based on current locale.
     */
    public function getNameAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * The trips that belong to the category.
     */
    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'trip_category_trip');
    }
}
