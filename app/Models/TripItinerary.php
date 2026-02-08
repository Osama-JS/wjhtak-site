<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripItinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'day_number',
        'title',
        'description',
        'image_path'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
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
}
