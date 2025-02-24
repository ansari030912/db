<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleTrainingCourse extends Model
{
    use HasFactory;

    protected $primaryKey = 'course_id';
    public $incrementing = false;

    protected $fillable = [
        'course_id',  // Add this line
        'perma',
        'title',
        'image',
        'duration_milliseconds',
        'duration',
        'exam_id',
        'price',
        'full_price',
        'cart',
        'lectures'
    ];

   // Define relationship to sections (one course has many sections)
    public function sections()
    {
        return $this->hasMany(SingleTrainingCourseSection::class, 'course_id', 'course_id');
    }

    // Define relationship to exam (each course belongs to one exam)
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
}
