<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\TrainingCourse;

class GetTrainingCourses extends Command
{
    protected $signature = 'fetch:training-courses';
    protected $description = 'Fetch training courses from API and update the database accordingly';

    public function handle()
    {
        $apiUrl = 'https://certsgang.com/v1/training-courses';
        $apiKey = 'b46279cb-13bb-4445-a6f9-6f252b61ae79';

        try {
            $response = Http::withHeaders(['x-api-key' => $apiKey])->get($apiUrl);

            if (!$response->successful()) {
                Log::error("API call failed: {$response->status()} - {$response->body()}");
                $this->error('API call failed. Check logs for details.');
                return 1;
            }

            $data = $response->json();

            if (!is_array($data)) {
                Log::error('Invalid API response format.');
                $this->error('Invalid API response format.');
                return 1;
            }

            $insertData = [];
            $courseIdentifiers = [];

            foreach ($data as $vendor) {
                if (empty($vendor['vendor_perma']) || empty($vendor['training_courses']) || !is_array($vendor['training_courses'])) {
                    Log::warning("Skipping invalid vendor data: " . json_encode($vendor));
                    continue;
                }

                foreach ($vendor['training_courses'] as $course) {
                    if (empty($course['title']) || empty($course['perma']) || empty($course['exam_id'])) {
                        Log::warning("Skipping invalid training course data: " . json_encode($course));
                        continue;
                    }

                    $courseIdentifiers[] = [
                        'vendor_perma' => $vendor['vendor_perma'],
                        'title' => $course['title'],
                    ];

                    $insertData[] = [
                        'vendor_title' => $vendor['vendor_title'] ?? null,
                        'vendor_perma' => $vendor['vendor_perma'],
                        'title' => $course['title'],
                        'perma' => $course['perma'],
                        'image' => $course['image'] ?? null,
                        'videos' => $course['videos'] ?? 0,
                        'duration_milliseconds' => $course['duration_milliseconds'] ?? 0,
                        'duration' => $course['duration'] ?? '',
                        'exam_id' => $course['exam_id'],
                        'updated_at' => now(),
                        'created_at' => now(),
                    ];
                }
            }

            // Bulk upsert to optimize performance
            if (!empty($insertData)) {
                TrainingCourse::upsert($insertData, ['vendor_perma', 'title'], [
                    'perma',
                    'image',
                    'videos',
                    'duration_milliseconds',
                    'duration',
                    'exam_id',
                    'updated_at',
                ]);
                $this->info(count($insertData) . ' training courses updated/inserted.');
            } else {
                $this->info('No valid training courses found.');
            }

            // Delete outdated courses
            $deletedCourses = TrainingCourse::whereNotIn('vendor_perma', array_column($courseIdentifiers, 'vendor_perma'))
                ->whereNotIn('title', array_column($courseIdentifiers, 'title'))
                ->delete();

            if ($deletedCourses > 0) {
                $this->info("$deletedCourses outdated courses removed from the database.");
            }

            return 0;
        } catch (\Exception $e) {
            Log::error('Exception occurred: ' . $e->getMessage());
            $this->error('An error occurred. Check logs for details.');
            return 1;
        }
    }
}
