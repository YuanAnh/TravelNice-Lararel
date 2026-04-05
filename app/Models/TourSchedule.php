<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourSchedule extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tour_id', 'day_number', 'title',
        'description', 'meals', 'accommodation',
    ];

    // ===== RELATIONSHIPS =====

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
