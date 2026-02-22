<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'notes',
        'active',
    ];


    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Scope a query to only include active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
