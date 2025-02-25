<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\RecentlyUpdated;

class FetchRecentlyUpdatedExams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:recently-updated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch recently updated exams from API and save to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiUrl = 'https://certsgang.com/v1/recently-updated';
        $apiKey = 'b46279cb-13bb-4445-a6f9-6f252b61ae79'; // Ensure you have the API key set in `config/services.php`

        $this->info('Fetching recently updated exams from API...');

        $response = Http::withHeaders([
            'x-api-key' => $apiKey
        ])->get($apiUrl);

        if ($response->failed()) {
            $this->error('Failed to fetch data. API responded with status: ' . $response->status());
            return;
        }

        $data = $response->json();

        if (!is_array($data)) {
            $this->error('Invalid data format received.');
            return;
        }

        foreach ($data as $exam) {
            RecentlyUpdated::updateOrCreate(
                ['exam_perma' => $exam['exam_perma']], // Ensure uniqueness
                [
                    'exam_code' => $exam['exam_code'],
                    'exam_title' => $exam['exam_title'],
                    'exam_questions' => $exam['exam_questions'] ?? null,
                    'exam_update_date' => $exam['exam_update_date'],
                    'exam_vendor_title' => $exam['exam_vendor_title'],
                    'exam_vendor_perma' => $exam['exam_vendor_perma'],
                    'exam_vendor_img' => $exam['exam_vendor_img'],
                ]
            );
        }

        $this->info('Recently updated exams successfully saved.');
    }
}
