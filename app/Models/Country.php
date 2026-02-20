<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     */
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'nicename',
        'numcode',
        'phonecode',
        'flag',
        'landmark_image',
        'active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get localized name based on current locale.
     */
    public function getNameAttribute(): ?string
    {
        return $this->nicename ?? $this->name;
    }

    /**
     * Get the cities for this country.
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function Trip()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Get active cities count.
     */
    public function activeCitiesCount(): int
    {
        return $this->cities()->where('active', true)->count();
    }

    /**
     * Get the trips to this country.
     */
    public function trips()
    {
        return $this->hasMany(Trip::class, 'to_country_id');
    }

    /**
     * Scope a query to only include active countries.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get flag URL.
     */
    public function getFlagUrlAttribute(): string
    {
        if ($this->flag) {
            return asset('storage/' . $this->flag);
        }
        return asset('images/flags/default.png');
    }

    /**
     * Get landmark image URL.
     */
    public function getLandmarkImageUrlAttribute(): string
    {
        if ($this->landmark_image) {
            return asset('storage/' . $this->landmark_image);
        }

        // Fallback to ISO-based image if available, otherwise placeholder
        if ($this->iso) {
            $path = 'images/destinations/' . strtolower($this->iso) . '.jpg';
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return asset('images/demo/destination-placeholder.jpg');
    }
}
