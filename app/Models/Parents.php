<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class Parents extends Authenticatable
{
    use Notifiable, HasPushSubscriptions;

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