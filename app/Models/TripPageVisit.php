<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripPageVisit extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'trip_id', 'ip'];

    public $timestamps = false; // Migration didn't show timestamps for this table

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
