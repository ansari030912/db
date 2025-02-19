<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamTopic extends Model
{
    use HasFactory;

    protected $table = 'exam_topics';
    public $timestamps = false;

    protected $fillable = ['exam_id', 'topic', 'topic_questions'];

    public function singleExam()
    {
        return $this->belongsTo(SingleExam::class, 'exam_id', 'exam_id');
    }
}
