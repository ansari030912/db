<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Blog;
use App\Models\SingleBlog;

class FetchSingleBlogs extends Command
{
    protected $signature = 'fetch:singleblogs';
    protected $description = 'Sync single blog details from API based on blogs table blog_perma';

    public function handle()
    {
        // Retrieve all blog_perma values from the blogs table
        $blogs = Blog::all(['blog_perma']);
        $apiBlogPermas = [];

        foreach ($blogs as $blog) {
            $apiBlogPermas[] = $blog->blog_perma;

            // Build the API URL using blog_perma
            $url = "https://certsgang.com/v1/blog/{$blog->blog_perma}";
            $response = Http::withHeaders([
                'x-api-key' => 'b46279cb-13bb-4445-a6f9-6f252b61ae79'
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();

                // Update or create the record in single_blogs using blog_perma as unique key
                SingleBlog::updateOrCreate(
                    ['blog_perma' => $data['blog_perma']],
                    [
                        'blog_title'        => $data['blog_title'],
                        'blog_summary'      => $data['blog_summary'],
                        'blog_content'      => $data['blog_content'] ?? null,
                        'blog_image'        => $data['blog_image'],
                        'blog_vendors'      => $data['blog_vendors'],
                        'blog_views'        => $data['blog_views'],
                        'blog_publish_date' => $data['blog_publish_date'],
                        'blog_update_date'  => $data['blog_update_date'],
                        'blog_id'           => $data['blog_id'],
                        'blog_vendors_list' => json_encode($data['blog_vendors_list']),
                    ]
                );

                $this->info("Synced single blog: {$data['blog_perma']}");
            } else {
                $this->error("Failed to fetch single blog for: {$blog->blog_perma}. HTTP status: " . $response->status());
            }
        }

        // Delete any records in single_blogs that do not correspond to a blog in blogs table
        SingleBlog::whereNotIn('blog_perma', $apiBlogPermas)->delete();

        $this->info('Single blogs synced successfully.');
    }
}
