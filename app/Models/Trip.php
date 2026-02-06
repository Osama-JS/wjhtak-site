<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_public' => 'boolean',
        'is_ad' => 'boolean',
        'active' => 'boolean', // Assuming there's an active column based on controller usage, but migration has is_public. Let's add accessor or check.
        'expiry_date' => 'date',
        'price' => 'decimal:2',
        'price_before_discount' => 'decimal:2',
    ];

    /**
     * Get the company that owns the trip.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the country where the trip starts.
     */
    public function fromCountry()
    {
        return $this->belongsTo(Country::class, 'from_country_id');
    }

    /**
     * Get the city where the trip starts.
     */
    public function fromCity()
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    /**
     * Get the country where the trip goes.
     */
    public function toCountry()
    {
        return $this->belongsTo(Country::class, 'to_country_id');
    }

      /**
     * Get the city where the trip goes (assuming to_city_id exists, need to check).
     * Migration didn't verify to_city_id but controller uses it. Let's check migration again carefully.
     * Migration ONLY has from_city_id. NO to_city_id.
     * Use toCountry only? Or maybe logic assumes destination is country-wide?
     * Controller uses: ->with(['fromCountry', 'toCountry', 'fromCity', 'toCity'...
     * Wait, controller assumes toCity exists. If not in DB, this will fail.
     * I should NOT add toCity relationship if column doesn't exist.
     * Let me re-read migration.
     */
    // Migration lines 30-32:
    // $table->foreignId('from_country_id')...
    // $table->foreignId('from_city_id')...
    // $table->foreignId('to_country_id')...
    // NO to_city_id.

    // So I will NOT add toCity relationship. I will need to fix Controller to not load 'toCity'.

    /**
     * Get the trip images.
     */
    public function images()
    {
        return $this->hasMany(TripImage::class);
    }

    /**
     * Get the trip rates/reviews.
     */
    public function rates()
    {
        return $this->hasMany(TripRate::class); // Controller uses rates.user
    }

    /**
     * Get the trip page visits.
     */
    public function pageVisits()
    {
        return $this->hasMany(TripPageVisit::class);
    }

    /**
     * Get the average rating.
     */
    public function getAverageRatingAttribute()
    {
        return $this->rates()->avg('rate') ?? 0;
    }

    /**
     * Get first image or default
     */
    public function getImageUrlAttribute()
    {
        $image = $this->images()->first();
        if ($image) {
            return asset('storage/' . $image->image_path);
        }
        return asset('images/defaults/trip_placeholder.jpg');

    }

    /**
     * Scope for active trips
     * Controller uses ->where('active', true)
     * But schema has 'is_public'. I should map active to is_public or create a scope.
     * Let's use is_public as the source of truth for "active".
     */
    public function scopeActive($query)
    {
        return $query->where('is_public', true)
                     ->where(function ($q) {
                         $q->whereNull('expiry_date')
                           ->orWhere('expiry_date', '>=', now()->toDateString());
                     });
    }

    /**
     * Accessor for 'active' attribute to support $trip->active check if needed
     */
    public function getActiveAttribute()
    {
        return $this->is_public && ($this->expiry_date === null || $this->expiry_date >= now());
    }
}
