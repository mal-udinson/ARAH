<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NaraMessage extends Model
{
    protected $fillable = ['conversation_id', 'role', 'message'];
}