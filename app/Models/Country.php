<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     */
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'nicename',
        'numcode',
        'phonecode',
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
        return app()->getLocale() === 'name' ? $this->name : $this->nicename;
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
