<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'show_from',
        'show_until',
        'status',
        'show_as_popup',
        'created_by',
        'created_by_name',
    ];

    protected $casts = [
        'show_from'     => 'date',
        'show_until'    => 'date',
        'show_as_popup' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * The single announcement that should currently be shown as a popup
     * to parents/students (most recent active one).
     */
    public static function currentPopup()
    {
        $today = now()->toDateString();

        return self::active()
            ->where('show_as_popup', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('show_from')->orWhereDate('show_from', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('show_until')->orWhereDate('show_until', '>=', $today);
            })
            ->latest('id')
            ->first();
    }
}