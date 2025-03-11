<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    // Set blog_perma as the primary key
    protected $primaryKey = 'blog_perma';
    public $incrementing = false; // blog_perma is a string, not auto-incrementing
    protected $keyType = 'string';

    protected $fillable = [
        'blog_title',
        'blog_perma',
        'blog_summary',
        // 'blog_content', // Include this if you're storing blog content
        'blog_image',
        'blog_vendors',
        'blog_views',
        'blog_publish_date',
        'blog_update_date',
        'blog_id',
        'blog_vendors_list',
    ];
}
