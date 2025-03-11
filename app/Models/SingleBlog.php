<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Blog;

class SingleBlog extends Model
{
    use HasFactory;

    protected $table = 'single_blogs';

    // The primary key is the auto-incrementing "id" column
    protected $fillable = [
        'blog_perma',
        'blog_title',
        'blog_summary',
        'blog_content',
        'blog_image',
        'blog_vendors',
        'blog_views',
        'blog_publish_date',
        'blog_update_date',
        'blog_id',
        'blog_vendors_list',
    ];

    /**
     * Optional relation if you want to reference the parent blog.
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_perma', 'blog_perma');
    }
}
