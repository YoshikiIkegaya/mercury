<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'user_id',
        'evaluated_by',
        'rate',
        'comment'
    ];
}
