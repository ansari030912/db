<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'vendor_title',
        'vendor_perma',
        'exam_code',
        'exam_title',
        'exam_perma',
        'exam_id'
    ];
}
