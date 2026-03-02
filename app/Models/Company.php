<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'en_name',
        'logo',
        'email',
        'phone',
        'phone_code',
        'notes',
        'active',
    ];


    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('images/demo/company-placeholder.jpg');
    }

    public function getLocalizedNameAttribute()
    {
        return (app()->getLocale() == 'en' && $this->en_name) ? $this->en_name : $this->name;
    }

    /**
     * Get company agents
     */
    public function agents()
    {
        return $this->hasMany(User::class)->where('user_type', User::TYPE_AGENT);
    }

    /**
     * Get company trips
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Scope a query to only include active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
