<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPassenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_booking_id',
        'name',
        'phone',
        'passport_number',
        'passport_expiry',
        'nationality',
    ];

    protected $casts = [
        'passport_expiry' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(TripBooking::class, 'trip_booking_id');
    }
}
