<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'kampus_id',
        'role',
        'action',
        'module',
        'description',
        'method',
        'route_name',
        'route_path',
        'target_type',
        'target_id',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kampus(): BelongsTo
    {
        return $this->belongsTo(Kampus::class);
    }
}