<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TboCity extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_code',
        'name',
        'name_ar',
        'country_code',
        'country_name',
        'country_name_ar',
    ];

    /**
     * Scope a query to search by name or country (EN and AR).
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'LIKE', "%{$term}%")
                     ->orWhere('name_ar', 'LIKE', "%{$term}%")
                     ->orWhere('country_name', 'LIKE', "%{$term}%")
                     ->orWhere('country_name_ar', 'LIKE', "%{$term}%");
    }
}
