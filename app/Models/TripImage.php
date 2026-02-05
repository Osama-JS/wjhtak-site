<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripImage extends Model
{

 protected $fillable = [
       'trip_id',
       'image_path',
    ];


    public function images()
    {
        return $this->hasMany(TripImage::class);
    }
}
