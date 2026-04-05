<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_id', 'category_id', 'title', 'slug',
        'description', 'duration_days', 'price_adult', 'price_child',
        'max_slots', 'thumbnail', 'cancel_policy', 'status', 'avg_rating',
    ];

    protected $casts = [
        'price_adult' => 'decimal:2',
        'price_child' => 'decimal:2',
        'avg_rating'  => 'decimal:2',
    ];

    // ===== RELATIONSHIPS =====

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function category()
    {
        return $this->belongsTo(TourCategory::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(TourImage::class)->orderBy('sort_order');
    }

    public function schedules()
    {
        return $this->hasMany(TourSchedule::class)->orderBy('day_number');
    }

    public function slots()
    {
        return $this->hasMany(TourSlot::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistedByUsers()
    {
        return $this->belongsToMany(User::class, 'wishlists');
    }

    // ===== HELPERS =====

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function availableSlots()
    {
        return $this->slots()->where('status', 'open')->get();
    }

    public function formattedPrice(): string
    {
        return number_format($this->price_adult, 0, ',', '.') . 'đ';
    }
}
