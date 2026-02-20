<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Trip;
use Illuminate\Http\Request;

class Trip extends Model
{
    use SoftDeletes;

    protected $fillable = [
       'title',
       'tickets',
       'description',
       'is_public',
       'company_id',
       'is_ad',
       'duration',
       'price',
       'price_before_discount',
       'expiry_date',
       'personnel_capacity',
       'from_country_id',
       'from_city_id',
       'to_country_id',
       'to_city_id',
       'admin_id',
       'profit',
       'percentage_profit_margin',
       'active',
       'page_visits',
       'base_capacity',
       'extra_passenger_price',
    ];


    protected $casts = [
        'active' => 'boolean',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function fromCountry() {
        return $this->belongsTo(Country::class, 'from_country_id');
    }

    public function toCountry() {
        return $this->belongsTo(Country::class, 'to_country_id');
    }

    public function toCity() {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    public function fromCity() {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    public function scopeDeactivateExpired($query)
    {
        return $query->where('expiry_date', '<', now())
                    ->where('active', true)
                    ->update(['active' => false]);
    }

    /**
     * Get the trip images.
     */
    public function images()
    {
        return $this->hasMany(TripImage::class);
    }

    public function banner()
    {
        return $this->hasMany(Banner::class, 'trip_id');
    }


    /**
     * Get the trip rates/reviews.
     */
    public function rates()
    {
        return $this->hasMany(TripRate::class);
    }

    public function itineraries()
    {
        return $this->hasMany(TripItinerary::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Get the trip page visits.
     */
    public function pageVisits()
    {
        return $this->hasMany(TripPageVisit::class);
    }

    /**
     * Get image URL.
     */
    public function getImageUrlAttribute()
    {
        $image = $this->images()->first();
        if ($image) {
            return asset('storage/' . $image->image_path);
        }
        return asset('images/default-placeholder.svg');
    }

    /**
     * Scope for active trips
     */
    public function scopeActive($query)
    {
        return $query->where('is_public', true)
                     ->where(function ($q) {
                         $q->whereNull('expiry_date')
                           ->orWhere('expiry_date', '>=', now()->toDateString());
                     });
    }

}
