<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\TrainingCourse;
use App\Models\TrainingCourseVendor;
use App\Models\Exam;

class GetTrainingCourses extends Command
{
    protected $signature = 'fetch:training-courses';
    protected $description = 'Fetch training courses from API and update the database accordingly';

    public function handle()
    {
        $apiUrl = 'https://certsgang.com/v1/training-courses';
        $apiKey = 'b46279cb-13bb-4445-a6f9-6f252b61ae79';

        try {
            // Fetch data from API
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

            $courseIdentifiers = [];
            $insertData = [];

            foreach ($data as $vendor) {
                if (empty($vendor['vendor_perma']) || empty($vendor['training_courses']) || !is_array($vendor['training_courses'])) {
                    Log::warning("Skipping invalid vendor data: " . json_encode($vendor));
                    continue;
                }

                // Insert or update vendor
                $vendorRecord = TrainingCourseVendor::updateOrCreate(
                    ['vendor_perma' => $vendor['vendor_perma']],
                    ['vendor_title' => $vendor['vendor_title'] ?? '']
                );

                foreach ($vendor['training_courses'] as $course) {
                    if (empty($course['title']) || empty($course['perma']) || empty($course['exam_id'])) {
                        Log::warning("Skipping invalid training course data: " . json_encode($course));
                        continue;
                    }

                    // Validate if exam exists
                    $examExists = Exam::where('exam_id', $course['exam_id'])->exists();
                    if (!$examExists) {
                        Log::warning("Skipping course '{$course['title']}' - Exam ID {$course['exam_id']} does not exist.");
                        continue;
                    }

                    $courseIdentifiers[] = $course['perma'];

                    $insertData[] = [
                        'vendor_id' => $vendorRecord->id, // Foreign key to training_course_vendors
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

            // Bulk upsert training courses
            if (!empty($insertData)) {
                TrainingCourse::upsert($insertData, ['perma'], [
                    'vendor_id',
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

            // Delete outdated courses that are not in the latest API response
            $deletedCourses = TrainingCourse::whereNotIn('perma', $courseIdentifiers)->delete();

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
