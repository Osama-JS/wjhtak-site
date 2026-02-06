<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
=======
>>>>>>> 3bbb2775a700e194ea5fcea955b258fbb44a9bd4
use Illuminate\Database\Eloquent\Model;

class TripImage extends Model
{
<<<<<<< HEAD
    use HasFactory;

    protected $fillable = ['trip_id', 'image_path'];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
=======

 protected $fillable = [
       'trip_id',
       'image_path',
    ];


    public function images()
    {
        return $this->hasMany(TripImage::class);
>>>>>>> 3bbb2775a700e194ea5fcea955b258fbb44a9bd4
    }
}
