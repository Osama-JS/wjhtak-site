<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'phone_code',
        'flag',
        'active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get localized name based on current locale.
     */
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * Get the cities for this country.
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    /**
     * Get active cities count.
     */
    public function activeCitiesCount(): int
    {
        return $this->cities()->where('active', true)->count();
    }

    /**
     * Scope a query to only include active countries.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get flag URL.
     */
    public function getFlagUrlAttribute(): string
    {
        if ($this->flag) {
            return asset('storage/' . $this->flag);
        }
        return asset('images/flags/default.png');
    }
}
