<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelBooking extends Model
{
    use HasFactory, SoftDeletes;

    // Booking status constants (internal - payment tracking)
    public const STATUS_DRAFT     = 'draft';
    public const STATUS_PENDING   = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED    = 'failed';

    // Booking state constants (display to user)
    public const STATE_AWAITING_PAYMENT = 'awaiting_payment';
    public const STATE_PREPARING        = 'preparing';
    public const STATE_CONFIRMED        = 'confirmed';
    public const STATE_CANCELLED        = 'cancelled';

    protected $fillable = [
        'user_id',
        'tbo_booking_id',
        'tbo_session_id',
        'tbo_result_token',
        'hotel_code',
        'hotel_name',
        'hotel_name_ar',
        'hotel_address',
        'star_rating',
        'city_id',
        'city_name',
        'country_code',
        'room_type_code',
        'room_type_name',
        'rooms_count',
        'adults',
        'children',
        'check_in_date',
        'check_out_date',
        'nights_count',
        'total_price',
        'currency',
        'status',
        'booking_state',
        'cancellation_policy',
        'tbo_raw_search',
        'tbo_raw_prebook',
        'tbo_raw_booking',
        'cancellation_reason',
        'notes',
    ];

    protected $casts = [
        'check_in_date'       => 'date',
        'check_out_date'      => 'date',
        'total_price'         => 'decimal:2',
        'cancellation_policy' => 'array',
        'tbo_raw_search'      => 'array',
        'tbo_raw_prebook'     => 'array',
        'tbo_raw_booking'     => 'array',
        'adults'              => 'integer',
        'children'            => 'integer',
        'rooms_count'         => 'integer',
        'nights_count'        => 'integer',
        'star_rating'         => 'integer',
    ];

    // =====================
    // Relationships
    // =====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guests()
    {
        return $this->hasMany(HotelBookingGuest::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'hotel_booking_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'hotel_booking_id')->latest();
    }

    public function histories()
    {
        return $this->hasMany(HotelBookingHistory::class);
    }

    // =====================
    // Accessors
    // =====================

    /**
     * Human-readable booking status in Arabic.
     */
    public function getStatusLabelArabicAttribute(): string
    {
        return match($this->booking_state) {
            self::STATE_AWAITING_PAYMENT => 'في انتظار الدفع',
            self::STATE_PREPARING        => 'قيد المعالجة',
            self::STATE_CONFIRMED        => 'مؤكد',
            self::STATE_CANCELLED        => 'ملغى',
            default                      => $this->booking_state,
        };
    }

    /**
     * Check if the booking is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if cancellation is allowed based on policy.
     */
    public function isCancellable(): bool
    {
        if (!$this->isPaid()) {
            return false;
        }

        $policy = $this->cancellation_policy;
        if (empty($policy)) {
            return true; // No policy = allow
        }

        // Check if today is before the deadline
        $deadline = $policy['deadline'] ?? null;
        if ($deadline && now()->greaterThan($deadline)) {
            return false;
        }

        return true;
    }
}
