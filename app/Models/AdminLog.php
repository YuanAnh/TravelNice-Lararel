<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'admin_id', 'action', 'target_type', 'target_id', 'description', 'ip_address', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Helper để tạo log dễ dàng
    public static function log(string $action, string $description, ?int $targetId = null, ?string $targetType = null): void
    {
        if (!auth()->check()) return;

        static::create([
            'admin_id'    => auth()->id(),
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'created_at'  => now(),
        ]);
    }
}