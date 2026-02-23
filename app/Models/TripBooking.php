<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_id',
        'status', // pending, confirmed, cancelled
        'total_price',
        'booking_date',
        'tickets_count',
        'notes'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'tickets_count' => 'integer',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function passengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'trip_booking_id');
    }
}
