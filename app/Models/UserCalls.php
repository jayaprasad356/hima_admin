<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCalls extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'call_user_id',
        'type',
        'started_time',
        'ended_time',
        'coins_spend',
        'income',
        'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plans::class);
    }
}
