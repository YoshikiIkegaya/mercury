<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'image_data', 'fcm_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // public function plan(){
    //     return $this->hasMany(Plan::class);
    // }

    public function plans()
    {
        return $this->belongsToMany('App\Models\Plan')->withTimestamps();
    }

    public function evaluations()
    {
        return $this->hasMany('App\Evaluation');
    }
}
