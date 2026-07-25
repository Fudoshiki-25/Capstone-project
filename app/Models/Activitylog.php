<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'details',
        'type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convenience helper so any controller can log an action in one line:
     *   ActivityLog::record($request->user(), 'Approved Application', 'Applicant: Juan Dela Cruz', 'success');
     */
    public static function record($user, string $action, ?string $details = null, string $type = 'info'): self
    {
        return self::create([
            'user_id'   => $user?->id,
            'user_name' => $user?->name ?? ($user ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) : 'System'),
            'user_role' => $user?->role ?? 'System',
            'action'    => $action,
            'details'   => $details,
            'type'      => $type,
        ]);
    }
}