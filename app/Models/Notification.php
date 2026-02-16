<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'title',
        'content',
        'icon',
        'user_id',
        'data',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    // ─── Notification Type Constants ───────────────────────

    // Storable types (saved to database)
    const TYPE_BOOKING_CONFIRMED   = 'booking_confirmed';
    const TYPE_BOOKING_CANCELLED   = 'booking_cancelled';
    const TYPE_PAYMENT_SUCCESS     = 'payment_success';
    const TYPE_PAYMENT_FAILED      = 'payment_failed';
    const TYPE_BOOKING_REMINDER    = 'booking_reminder';
    const TYPE_FAVORITE_TRIP_UPDATE = 'favorite_trip_update';

    // Non-storable types (push only, no database record)
    const TYPE_NEW_TRIP   = 'new_trip';
    const TYPE_PROMOTION  = 'promotion';
    const TYPE_GENERAL    = 'general';

    /**
     * Types that should be persisted in the database.
     */
    const STORABLE_TYPES = [
        self::TYPE_BOOKING_CONFIRMED,
        self::TYPE_BOOKING_CANCELLED,
        self::TYPE_PAYMENT_SUCCESS,
        self::TYPE_PAYMENT_FAILED,
        self::TYPE_BOOKING_REMINDER,
        self::TYPE_FAVORITE_TRIP_UPDATE,
    ];

    // ─── Relationships ─────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // ─── Helpers ───────────────────────────────────────────

    /**
     * Check if a given notification type should be stored.
     */
    public static function shouldStore(string $type): bool
    {
        return in_array($type, self::STORABLE_TYPES);
    }

    /**
     * Get the icon name/path based on notification type.
     */
    public static function iconForType(string $type): string
    {
        return match ($type) {
            self::TYPE_BOOKING_CONFIRMED   => 'booking_confirmed',
            self::TYPE_BOOKING_CANCELLED   => 'booking_cancelled',
            self::TYPE_PAYMENT_SUCCESS     => 'payment_success',
            self::TYPE_PAYMENT_FAILED      => 'payment_failed',
            self::TYPE_BOOKING_REMINDER    => 'booking_reminder',
            self::TYPE_FAVORITE_TRIP_UPDATE => 'favorite_update',
            self::TYPE_NEW_TRIP            => 'new_trip',
            self::TYPE_PROMOTION           => 'promotion',
            default                        => 'general',
        };
    }
}
