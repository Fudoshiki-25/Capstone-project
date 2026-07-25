<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeEnrollmentSetting extends Model
{
    protected $fillable = [
        'grade_level',
        'is_open',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
        ];
    }
}
