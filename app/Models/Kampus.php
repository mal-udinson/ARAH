<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kampus extends Model
{
    protected $table = 'kampus';

    protected $fillable = [
        'nama',
        'kode',
        'email',
        'alamat',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function dosen(): HasMany
{
    return $this->hasMany(Dosen::class);
}
}