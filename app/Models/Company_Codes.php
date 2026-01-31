<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_Codes extends Model
{

    use HasFactory;


   protected $table = 'company_codes';

   protected $fillable = [
        'company_id',
        'code',
        'type',
        'value',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'value'  => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
