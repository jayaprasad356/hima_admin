<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_calls extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'call_user_id',
        'end_datetime',
        'start_datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }
}

