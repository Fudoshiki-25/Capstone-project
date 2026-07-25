<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = [
        'grade_level',
        'name',
        'school_year',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class, 'section_id');
    }
}
