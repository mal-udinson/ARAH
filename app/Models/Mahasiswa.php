<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
protected $table = 'mahasiswa';    
protected $fillable = [
    'user_id',
    'kampus_id',
    'dosen_pa_id',
    'nim',
    'nama',
    'semester',
    'ipk',
];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function nilai(): HasMany { return $this->hasMany(Nilai::class); }
    public function asesmen(): HasMany { return $this->hasMany(AsesmenMinat::class); }
    public function rekomendasi(): HasMany { return $this->hasMany(Rekomendasi::class); }

    public function kampus()
{
    return $this->belongsTo(Kampus::class);
}

public function dosenPa()
{
    return $this->belongsTo(Dosen::class, 'dosen_pa_id');
}
}
    