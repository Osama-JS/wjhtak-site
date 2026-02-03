<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Trip;
use Illuminate\Http\Request;

class Trip extends Model
{
    use SoftDeletes;

    protected $fillable = [
       'title',
       'tickets',
       'description',
       'is_public',
       'company_id',
       'is_ad',
       'duration',
       'price',
       'price_before_discount',
       'expiry_date',
       'personnel_capacity',
       'from_country_id',
       'from_city_id',
       'to_country_id',
       'admin_id',
       'profit',
       'percentage_profit_margin',
       'active',
    ];


    protected $casts = [
        'active' => 'boolean',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function fromCountry() {
        return $this->belongsTo(Country::class, 'from_country_id');
    }

    public function toCountry() {
        return $this->belongsTo(Country::class, 'to_country_id');
    }

    public function fromCity() {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    public function scopeDeactivateExpired($query)
    {
        return $query->where('expiry_date', '<', now())
                    ->where('active', true)
                    ->update(['active' => false]);
    }

}
