<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamPrice extends Model
{
    use HasFactory;

    protected $table = 'exam_prices';
    public $timestamps = false;

    protected $fillable = ['exam_id', 'type', 'title', 'cart', 'price', 'full_price', 'off'];

    public function singleExam()
    {
        return $this->belongsTo(SingleExam::class, 'exam_id', 'exam_id');
    }
}
