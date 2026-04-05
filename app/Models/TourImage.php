<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourImage extends Model
{
    public $timestamps = false;

    protected $fillable = ['tour_id', 'image_path', 'caption', 'sort_order'];

    // ===== RELATIONSHIPS =====

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
