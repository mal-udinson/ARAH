<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $table = 'prodi';

    protected $fillable = ['kampus_id', 'nama'];

    public function kampus()
    {
        return $this->belongsTo(Kampus::class);
    }
}