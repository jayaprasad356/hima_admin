<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offerlocked extends Model
{
    protected $table = 'offer_locked'; // Adjust the table name if necessary

    protected $fillable = [
        'customer_id',
        'offer_id',
        'datetime', 
    ];

    public function offer()
    {
        return $this->belongsTo(Offers::class, 'offer_id');
    }
}

