<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleExam extends Model
{
    use HasFactory;

    protected $table = 'single_exam';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'exam_id',
        'exam_code',
        'exam_title',
        'exam_perma',
        'exam_questions',
        'exam_update_date',
        'exam_pdf',
        'exam_te',
        'exam_sg',
        'vendor_id',
        'vendor_title',
        'vendor_perma',
        'exam_article',
        'exam_pdf_price',
        'exam_ete_price',
        'exam_sg_price',
        'exam_sc_price',
        'is_disabled',
        'index_tag',
        'exam_preorder',
        'exam_last_week_passed',
        'exam_last_week_average_score',
        'exam_last_week_word_to_word',
        'exam_certs',
        'exam_training_course',
        'exam_redirect',
        'exam_alternate',
        'exam_retired'
    ];

    protected $casts = [
        'exam_certs' => 'array',
        'exam_training_course' => 'array',
        'exam_redirect' => 'array',
        'exam_alternate' => 'array',
    ];

    // Relationship with Exam (One-to-One)
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    // Relationship with Vendor (One-to-One)
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    // Relationship with Certifications (Many-to-Many)
    public function certifications()
    {
        return $this->belongsToMany(Certificate::class, 'exam_certificates', 'exam_id', 'cert_id')->withTimestamps();
    }
    public function prices()
    {
        return $this->hasMany(ExamPrice::class, 'exam_id', 'exam_id');
    }

    public function teImages()
    {
        return $this->hasMany(TeImage::class, 'exam_id', 'exam_id');
    }

    public function questionTypes()
    {
        return $this->hasMany(QuestionType::class, 'exam_id', 'exam_id');
    }

    public function topics()
    {
        return $this->hasMany(ExamTopic::class, 'exam_id', 'exam_id');
    }

    public function faqs()
    {
        return $this->hasMany(ExamFaq::class, 'exam_id', 'exam_id');
    }

}
