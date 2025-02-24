<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleTrainingCourseSection extends Model
{
    use HasFactory;

    protected $primaryKey = 'section_id';
    public $incrementing = false;

    protected $fillable = [
        'section_id',  // Add this line
        'course_id',
        'section_seq',
        'section_title',
        'section_lectures',
        'section_duration_milliseconds',
        'section_duration'
    ];

    // Define relationship to lectures (each section has many lectures)
    public function lectures()
    {
        return $this->hasMany(SingleTrainingCourseLecture::class, 'section_id', 'section_id');
    }

    // Define relationship to course (each section belongs to one course)
    public function course()
    {
        return $this->belongsTo(SingleTrainingCourse::class, 'course_id');
    }
}
