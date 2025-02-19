<?php

// app/Models/ExamCertificate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamCertificate extends Model
{
    use HasFactory;

    protected $table = 'exam_certificates';

    protected $fillable = [
        'exam_id',
        'cert_id',
        'vendor_id',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function certificate()
    {
        return $this->belongsTo(Certificate::class, 'cert_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
