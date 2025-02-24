<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Banner;

class FetchBannerCommand extends Command
{
    protected $signature = 'banner:fetch';
    protected $description = 'Fetch banner data from external API and update the database';

    public function handle()
    {
        $url = 'https://certsgang.com/v1/banner';
        $apiKey = 'b46279cb-13bb-4445-a6f9-6f252b61ae79';

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
        ])->get($url);

        if ($response->successful()) {
            $data = $response->json();

            Banner::updateOrCreate(
                ['banner_website' => $data['banner_website']],
                [
                    'banner_src' => $data['banner_src'],
                    'banner_link' => $data['banner_link'],
                ]
            );

            $this->info('Banner data updated successfully!');
        } else {
            $this->error('Failed to fetch banner data.');
        }
    }
}
