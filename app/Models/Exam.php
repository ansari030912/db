<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $primaryKey = 'exam_id'; // Primary key
    public $incrementing = false; // Since exam_id comes from API
    protected $keyType = 'int'; // Ensuring integer type

    protected $fillable = [
        'exam_id',
        'exam_code',
        'exam_title',
        'exam_perma',
        'exam_questions',
        'vendor_id',
        'vendor_title',
        'vendor_perma',
    ];

    // Relationship with Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    // Relationship with SingleExam (One-to-One)
    public function singleExam()
    {
        return $this->hasOne(SingleExam::class, 'exam_id', 'exam_id');
    }

    // Relationship with Certifications (Many-to-Many via exam_certificates)
    public function certifications()
    {
        return $this->belongsToMany(Certificate::class, 'exam_certificates', 'exam_id', 'cert_id')->withTimestamps();
    }
}
