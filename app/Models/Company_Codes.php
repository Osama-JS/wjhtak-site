<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company_Codes extends Model
{
   protected $fillable = [
        'company_id',
        'code',
        'type',
        'value',
        'active',
    ];

}
