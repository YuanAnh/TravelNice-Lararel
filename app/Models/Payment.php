<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'gateway', 'transaction_id',
        'amount', 'status', 'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // ===== HELPERS =====

    public function isSuccess(): bool { return $this->status === 'success'; }
    public function isPending(): bool { return $this->status === 'pending'; }
    public function isRefunded(): bool { return $this->status === 'refunded'; }
}
