<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Parents extends Authenticatable
{
    use Notifiable;

    protected $table = 'parents'; // ← ADD THIS LINE

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'profile_pic',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}