<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelBookingGuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_booking_id',
        'title',
        'first_name',
        'last_name',
        'type',
        'nationality',
        'passport_number',
        'passport_expiry',
        'dob',
    ];

    protected $casts = [
        'passport_expiry' => 'date',
        'dob'             => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(HotelBooking::class, 'hotel_booking_id');
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->title} {$this->first_name} {$this->last_name}";
    }

    /**
     * Format guest for TBO API booking payload.
     */
    public function toTboFormat(): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'first_name'       => $this->first_name,
            'last_name'        => $this->last_name,
            'type'             => $this->type,
            'nationality'      => $this->nationality,
            'passport_number'  => $this->passport_number,
            'passport_expiry'  => $this->passport_expiry?->format('Y-m-d'),
            'dob'              => $this->dob?->format('Y-m-d'),
        ];
    }
}
