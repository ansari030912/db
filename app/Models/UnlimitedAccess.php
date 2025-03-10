<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnlimitedAccess extends Model
{
    use HasFactory;

    protected $table = 'unlimited_access';

    protected $fillable = [
        'pdf_full_price',
        'pdf_price',
        'pdf_cart',
        'te_full_price',
        'te_price',
        'te_cart',
    ];
}
