<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EnrollmentRequirement extends Model
{
    protected $fillable = [
        'enrollment_id',
        'document_type',
        'document_label',
        'path',
        'status',
        'feedback',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * The enrollment record this document belongs to.
     */
    public function enrollment()
    {
        return $this->belongsTo(StudentEnrollment::class, 'enrollment_id');
    }

    /**
     * Public URL for the uploaded file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}