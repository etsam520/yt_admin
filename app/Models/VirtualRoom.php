<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualRoom extends Model
{
    //
    public $fillable = [
        'user_id',
        'room_token',
        'label',
        'set_id',
    ];

}   