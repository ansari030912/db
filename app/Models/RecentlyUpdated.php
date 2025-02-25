<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentlyUpdated extends Model
{
    use HasFactory;

    protected $table = 'recently_updated';

    protected $fillable = [
        'exam_code',
        'exam_title',
        'exam_perma',
        'exam_questions',
        'exam_update_date',
        'exam_vendor_title',
        'exam_vendor_perma',
        'exam_vendor_img',
    ];
}
