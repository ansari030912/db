<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $primaryKey = 'cert_id';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = [
        'cert_id',
        'cert_title',
        'cert_name',
        'cert_perma',
    ];

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_certificates', 'cert_id', 'exam_id')->withTimestamps();
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'exam_certificates', 'cert_id', 'vendor_id')->withTimestamps();
    }
}
