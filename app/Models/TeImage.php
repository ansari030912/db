<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeImage extends Model
{
    use HasFactory;

    protected $table = 'te_images';
    public $timestamps = false;

    protected $fillable = ['exam_id', 'te_img_id', 'te_img_src'];

    public function singleExam()
    {
        return $this->belongsTo(SingleExam::class, 'exam_id', 'exam_id');
    }
}
