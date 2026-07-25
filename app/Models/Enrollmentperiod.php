<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_year',
        'start_date',
        'end_date',
        'is_open',
        'enrollment_fee',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'is_open'        => 'boolean',
        'enrollment_fee' => 'decimal:2',
    ];

    /**
     * The single period currently flagged open. Falls back to the most
     * recently created row if none are explicitly open, so the dashboard
     * always has something to display instead of breaking on first run.
     */
    public static function current(): ?self
    {
        return self::where('is_open', true)->latest('id')->first()
            ?? self::latest('id')->first();
    }
}