<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchNotification extends Model
{
    protected $fillable = [
        'user_id',
        'creator_image_data',
        'room_id'
    ];
}
