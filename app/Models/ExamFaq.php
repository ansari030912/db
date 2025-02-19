<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamFaq extends Model
{
    use HasFactory;

    protected $table = 'exam_faqs';
    public $timestamps = false;

    protected $fillable = ['exam_id', 'faq_q', 'faq_a'];

    public function singleExam()
    {
        return $this->belongsTo(SingleExam::class, 'exam_id', 'exam_id');
    }
}
