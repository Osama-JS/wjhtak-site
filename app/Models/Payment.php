<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_booking_id',
        'hotel_booking_id',
        'user_id',
        'payment_gateway',
        'payment_method',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'raw_response',
        'invoice_path',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(TripBooking::class, 'trip_booking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotelBooking()
    {
        return $this->belongsTo(HotelBooking::class, 'hotel_booking_id');
    }
}
