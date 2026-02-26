<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingHistory extends Model
{
    protected $fillable = [
        'trip_booking_id',
        'user_id',
        'action',
        'description',
        'previous_state',
        'new_state',
    ];

    public function tripBooking()
    {
        return $this->belongsTo(TripBooking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
