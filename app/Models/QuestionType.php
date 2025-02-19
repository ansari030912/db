<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
{
    use HasFactory;

    protected $table = 'question_types';
    public $timestamps = false;

    protected $fillable = ['exam_id', 'question_type', 'question_type_count'];

    public function singleExam()
    {
        return $this->belongsTo(SingleExam::class, 'exam_id', 'exam_id');
    }
}
