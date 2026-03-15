<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelBookingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_booking_id',
        'user_id',
        'action',
        'description',
        'previous_state',
        'new_state',
    ];

    public function booking()
    {
        return $this->belongsTo(HotelBooking::class, 'hotel_booking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
