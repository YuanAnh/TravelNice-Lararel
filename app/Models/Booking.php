<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code', 'user_id', 'tour_slot_id',
        'num_adults', 'num_children', 'total_price',
        'discount_amount', 'note', 'status', 'cancelled_at',
    ];

    protected $casts = [
        'total_price'     => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'cancelled_at'    => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tourSlot()
    {
        return $this->belongsTo(TourSlot::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // ===== HELPERS =====

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isPaid(): bool      { return $this->status === 'paid'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }

    public function netTotal(): float
    {
        return $this->total_price - $this->discount_amount;
    }

    // Tự sinh booking_code trước khi tạo
    protected static function booted(): void
    {
        static::creating(function ($booking) {
            $booking->booking_code = 'TN' . strtoupper(substr(uniqid(), -6));
        });
    }
}
