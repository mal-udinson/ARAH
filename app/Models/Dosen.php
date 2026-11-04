<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    protected $table = 'dosen';

    protected $fillable = [
        'user_id',
        'kampus_id',
        'nip',
        'nama',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kampus(): BelongsTo
    {
        return $this->belongsTo(Kampus::class);
    }

    public function mahasiswa(): HasMany
    {
        return $this->hasMany(
            Mahasiswa::class,
            'dosen_pa_id'
        );
    }
}