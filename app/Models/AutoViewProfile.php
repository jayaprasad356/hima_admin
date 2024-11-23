<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AutoViewProfile extends Model
{
    use Notifiable;

    protected $table = 'auto_view_profile';


    protected $fillable = [
        'user_id',
        'view_user_id',
        'view_datetime',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }

}
