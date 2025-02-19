<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $primaryKey = 'vendor_id'; // Setting vendor_id as primary key
    public $incrementing = false; // Because vendor_id is not auto-incrementing
    protected $keyType = 'int'; // Ensures it is treated as an integer

    protected $fillable = [
        'vendor_id',
        'vendor_title',
        'vendor_perma',
        'vendor_img',
        'vendor_exams',
    ];
}
