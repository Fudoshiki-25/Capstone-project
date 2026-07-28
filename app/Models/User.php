<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'assigned_grade',
        'is_active',
        'last_login_at',
        'last_seen_at',
        'profile_photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'last_login_at'     => 'datetime',
            'last_seen_at'      => 'datetime',
        ];
    }

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    // Convenience: true for both admin and superadmin
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    /**
     * "Online" = made an authenticated request within the last few minutes.
     * last_seen_at is refreshed on every request by the TrackLastSeen
     * middleware, so this is a live-ish presence indicator, not just "has
     * an active account" (that's is_active, a separate concept).
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at !== null && $this->last_seen_at->gt(now()->subMinutes(3));
    }
}