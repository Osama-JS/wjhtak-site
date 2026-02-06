<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'banners';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
<<<<<<< HEAD
        'title',
        'desc',
        'image_path',
        'mobile_image_path',
        'link', // link is not in migration, but I'll keep it in fillable if needed elsewhere or check if I should add it
        'trip_id',
=======
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'image_path',
        'link',
        'trip_id',
        'sort_order',
>>>>>>> 3bbb2775a700e194ea5fcea955b258fbb44a9bd4
        'active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
<<<<<<< HEAD
=======
        'sort_order' => 'integer',
>>>>>>> 3bbb2775a700e194ea5fcea955b258fbb44a9bd4
    ];

    /**
     * Get image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return asset('images/banners/default.jpg');
    }

    /**
     * Get associated trip (if any).
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Scope a query to only include active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
