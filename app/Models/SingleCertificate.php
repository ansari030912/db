<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SingleCertificate extends Model
{
    protected $table = 'single_certificates';
    protected $primaryKey = 'cert_id';
    public $incrementing = false;
    protected $keyType = 'unsignedBigInteger';

    protected $fillable = [
        'cert_id',
        'has_multiple_exams',
        'cert_title',
        'cert_perma',
        'cert_full_name',
        'vendor_title',
        'vendor_perma',
        'is_disabled',
        'index_tag',
        'cert_single_exam',
    ];
}
