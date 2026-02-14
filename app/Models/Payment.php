<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_booking_id',
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
}
