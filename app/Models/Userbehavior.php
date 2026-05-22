<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBehavior extends Model
{
    protected $fillable = [
        'user_id', 'event_type', 'tour_id', 'destination',
        'category', 'duration_days', 'price_point', 'view_seconds', 'meta',
    ];

    protected $casts = ['meta' => 'array'];

    // ── Event types ──────────────────────────────────────────────
    const EVENT_TOUR_VIEW    = 'tour_view';
    const EVENT_TOUR_SEARCH  = 'tour_search';
    const EVENT_CHAT_MENTION = 'chat_mention';
    const EVENT_BOOKING      = 'booking';
    const EVENT_WISHLIST     = 'wishlist';

    // ── Relations ────────────────────────────────────────────────
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    // ── Scopes ───────────────────────────────────────────────────
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}