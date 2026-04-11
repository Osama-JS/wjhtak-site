<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripLike extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'trip_id'];

    // Timestamps are not essential for this pivot, but can be added if needed.
    // Given the migration used Blueprint::id(), timestamps might be expected by default unless disabled.
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
