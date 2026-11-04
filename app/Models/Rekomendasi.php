<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekomendasi extends Model
{
    protected $table = 'rekomendasi';

    protected $fillable = [
        'mahasiswa_id',
        'mata_kuliah_id',
        'skor',
        'alasan',
        'status',
    ];

    protected $casts = [
        'skor' => 'float',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }
}
