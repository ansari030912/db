<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Blog;

class FetchBlogs extends Command
{
    protected $signature = 'fetch:blogs';
    protected $description = 'Fetch blogs from API and sync the database with API data';

    public function handle()
    {
        $response = Http::withHeaders([
            'x-api-key' => 'b46279cb-13bb-4445-a6f9-6f252b61ae79'
        ])->get('https://certsgang.com/v1/blogs');

        if ($response->successful()) {
            $data = $response->json();
            $apiBlogPermas = [];

            foreach ($data['blogs'] as $blogData) {
                // Collect the blog_perma values from the API
                $apiBlogPermas[] = $blogData['blog_perma'];

                Blog::updateOrCreate(
                    ['blog_perma' => $blogData['blog_perma']],
                    [
                        'blog_title'        => $blogData['blog_title'],
                        'blog_summary'      => $blogData['blog_summary'],
                        // 'blog_content'      => $blogData['blog_content'] ?? null,
                        'blog_image'        => $blogData['blog_image'],
                        'blog_vendors'      => $blogData['blog_vendors'],
                        'blog_views'        => $blogData['blog_views'],
                        'blog_publish_date' => $blogData['blog_publish_date'],
                        'blog_update_date'  => $blogData['blog_update_date'],
                        'blog_id'           => $blogData['blog_id'],
                        // Convert the array to a JSON string before storing it
                        'blog_vendors_list' => json_encode($blogData['blog_vendors_list']),
                    ]
                );
            }

            // Delete any blog records that are not present in the API response
            Blog::whereNotIn('blog_perma', $apiBlogPermas)->delete();

            $this->info('Blogs fetched and synced successfully.');
        } else {
            $this->error('Failed to fetch blogs. HTTP status: ' . $response->status());
        }
    }
}
