<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'region', 'country', 'thumbnail'];

    // ===== RELATIONSHIPS =====

    public function tours()
    {
        return $this->hasMany(Tour::class);
    }
}
