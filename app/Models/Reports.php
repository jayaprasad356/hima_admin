<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    protected $fillable = [
        'user_id',
        'chat_user_id',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
    public function chatUser()
    {
        return $this->belongsTo(Users::class, 'chat_user_id');
    }
}
