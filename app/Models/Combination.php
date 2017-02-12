<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Combination extends Model
{
    protected $fillable = [
        'creator_id',
        'participant_id',
    ];

}
