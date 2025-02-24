<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HotExam;
use Illuminate\Support\Facades\Http;

class FetchHotExams extends Command
{
    protected $signature = 'fetch:hot-exams';
    protected $description = 'Fetch hot exams from external API and store them in the database';

    public function handle()
    {
        $apiUrl = 'https://certsgang.com/v1/hot_exams';
        $apiKey = 'b46279cb-13bb-4445-a6f9-6f252b61ae79';

        $response = Http::withHeaders([
            'x-api-key' => $apiKey
        ])->get($apiUrl);

        if ($response->failed()) {
            $this->error('Failed to fetch data from API');
            return;
        }

        $data = $response->json();

        if (!isset($data['week']) || !isset($data['month'])) {
            $this->error('Invalid response format');
            return;
        }

        // Insert week exams
        foreach ($data['week'] as $exam) {
            HotExam::updateOrCreate(
                ['exam_id' => $exam['exam_id']],
                [
                    'type' => 'week',
                    'vendor_title' => $exam['vendor_title'],
                    'vendor_perma' => $exam['vendor_perma'],
                    'exam_code' => $exam['exam_code'],
                    'exam_title' => $exam['exam_title'],
                    'exam_perma' => $exam['exam_perma'],
                    'exam_id' => $exam['exam_id']
                ]
            );
        }

        // Insert month exams
        foreach ($data['month'] as $exam) {
            HotExam::updateOrCreate(
                ['exam_id' => $exam['exam_id']],
                [
                    'type' => 'month',
                    'vendor_title' => $exam['vendor_title'],
                    'vendor_perma' => $exam['vendor_perma'],
                    'exam_code' => $exam['exam_code'],
                    'exam_title' => $exam['exam_title'],
                    'exam_perma' => $exam['exam_perma'],
                    'exam_id' => $exam['exam_id']
                ]
            );
        }

        $this->info('Hot exams have been successfully saved to the database.');
    }
}
