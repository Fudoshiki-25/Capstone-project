<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeTuitionFee extends Model
{
    protected $fillable = [
        'grade_level',
        'annual_amount',
    ];

    protected $casts = [
        'annual_amount' => 'decimal:2',
    ];
}
