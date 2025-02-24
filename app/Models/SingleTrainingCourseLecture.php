<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleTrainingCourseLecture extends Model
{
    use HasFactory;

    // Set the primary key to 'id' (default primary key for this model)
    protected $primaryKey = 'id';

    // Fillable attributes including 'lecture_id'
    protected $fillable = [
        'lecture_id',           // Adding 'lecture_id'
        'section_id',
        'lecture_seq',
        'lecture_title',
        'lecture_duration_timespan',
        'lecture_duration'
    ];

    // Define relationship to section (each lecture belongs to one section)
    public function section()
    {
        return $this->belongsTo(SingleTrainingCourseSection::class, 'section_id', 'section_id');
    }
}
