<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCourseVendor extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_title', 'vendor_perma'];

    public function courses()
    {
        return $this->hasMany(TrainingCourse::class, 'vendor_id', 'id');
    }
}
