<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{

    protected $fillable = [
        'time',
        'duration',
        'subject',
        'user_id',
        'ticket_id',
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
}
