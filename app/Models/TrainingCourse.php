<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'title',
        'perma',
        'image',
        'videos',
        'duration_milliseconds',
        'duration',
        'exam_id'
    ];

    public function vendor()
    {
        return $this->belongsTo(TrainingCourseVendor::class, 'vendor_id', 'id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }
}
