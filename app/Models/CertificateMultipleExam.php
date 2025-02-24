<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateMultipleExam extends Model
{
    protected $table = 'certificates_multiple_exams';
    protected $fillable = [
        'exam_id',
        'cert_id',
        'exam_title',
        'exam_perma',
        'exam_retired',
        'exam_questions',
        'exam_vendor_title',
        'exam_disabled',
        'exam_vendor_perma',
    ];
}
