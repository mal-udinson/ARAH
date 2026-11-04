<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsesmenMinat extends Model
{
    protected $table = 'asesmen_minat';

    protected $fillable = [
        'mahasiswa_id',
        'kategori',
        'skor',
    ];

    protected $casts = [
        'skor' => 'float',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}