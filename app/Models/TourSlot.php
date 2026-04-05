<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id', 'departure_date',
        'total_slots', 'booked_slots', 'status',
    ];

    protected $casts = [
        'departure_date' => 'date',
    ];

    // ===== RELATIONSHIPS =====

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // ===== HELPERS =====

    public function remainingSlots(): int
    {
        return $this->total_slots - $this->booked_slots;
    }

    public function isAvailable(): bool
    {
        return $this->status === 'open' && $this->remainingSlots() > 0;
    }
}
