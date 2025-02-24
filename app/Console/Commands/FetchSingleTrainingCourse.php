<?php

namespace App\Console\Commands;

use App\Models\SingleTrainingCourseLecture;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\TrainingCourse;
use App\Models\SingleTrainingCourse;
use App\Models\SingleTrainingCourseSection;
use Illuminate\Support\Facades\File;

class FetchSingleTrainingCourse extends Command
{
    protected $signature = 'fetch:single-training-courses';
    protected $description = 'Fetch training course data from external API and store it in the database';

    public function handle()
    {
        $apiKey = 'b46279cb-13bb-4445-a6f9-6f252b61ae79';
        $courses = TrainingCourse::select('perma')->get();
        $missingCourseIds = [];

        foreach ($courses as $course) {
            $perma = $course->perma;
            $apiUrl = "https://certsgang.com/v1/training-course/{$perma}";

            $this->info("Fetching data for: {$perma}");

            $response = Http::withHeaders([
                'x-api-key' => $apiKey
            ])->get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['course_id'])) {
                    Log::error("Missing course_id for {$perma}");
                    $missingCourseIds[] = $perma;
                    $this->info("Missing course_id for {$perma}, logged and continuing.");
                    continue;
                }

                // Update or create SingleTrainingCourse record
                $trainingCourse = SingleTrainingCourse::updateOrCreate(
                    ['course_id' => $data['course_id']],
                    [
                        'course_id' => $data['course_id'],
                        'perma' => $data['perma'],
                        'title' => $data['title'],
                        'image' => $data['image'],
                        'duration_milliseconds' => $data['duration_milliseconds'],
                        'duration' => $data['duration'],
                        'exam_id' => $data['exam_id'],
                        'price' => $data['price'] ?? null,
                        'full_price' => $data['full_price'] ?? null,
                        'cart' => $data['cart'] ?? null,
                        'lectures' => isset($data['sections']) ? array_sum(array_map(fn($s) => count($s['lectures'] ?? []), $data['sections'])) : 0
                    ]
                );

                // Save course sections
                if (!empty($data['sections'])) {
                    foreach ($data['sections'] as $section) {
                        if (!isset($section['section_id'])) {
                            Log::error("Missing section_id for course {$data['course_id']}");
                            continue;
                        }

                        $this->info("Saving section: " . $section['section_title']);

                        $sectionRecord = SingleTrainingCourseSection::updateOrCreate(
                            ['section_id' => $section['section_id']],
                            [
                                'section_id' => $section['section_id'],  // Explicitly include section_id here
                                'course_id' => $data['course_id'],
                                'section_seq' => $section['section_seq'] ?? null,
                                'section_title' => $section['section_title'],
                                'section_lectures' => count($section['lectures'] ?? []),
                                'section_duration_milliseconds' => $section['secion_duration_millseconds'] ?? null,
                                'section_duration' => $section['secion_duration'] ?? null
                            ]
                        );

                        // Save section lectures
                        if (!empty($section['lectures'])) {
                            foreach ($section['lectures'] as $lecture) {
                                if (!isset($lecture['lecture_id'])) {
                                    Log::error("Missing lecture_id for section {$section['section_id']}");
                                    continue;
                                }

                                $this->info("Saving lecture: " . $lecture['lecture_title']);

                                SingleTrainingCourseLecture::updateOrCreate(
                                    ['lecture_id' => $lecture['lecture_id']],
                                    [
                                        'lecture_id' => $lecture['lecture_id'],  // Explicitly include lecture_id here
                                        'section_id' => $sectionRecord->section_id,
                                        'lecture_seq' => $lecture['lecture_seq'] ?? null,
                                        'lecture_title' => $lecture['lecture_title'],
                                        'lecture_duration_timespan' => $lecture['lecture_duration_timespan'] ?? null,
                                        'lecture_duration' => $lecture['lecture_duration'] ?? null
                                    ]
                                );
                            }
                        }
                    }
                }

                $this->info("Successfully stored data for: {$perma}");
            } else {
                Log::error("Failed to fetch data for {$perma}: " . $response->body());
                $this->error("Failed to fetch data for: {$perma}");
            }
        }

        // Save missing course_ids to a JSON file
        if (!empty($missingCourseIds)) {
            $filePath = storage_path('app/missing_course_ids.json');
            if (!File::exists($filePath)) {
                File::put($filePath, json_encode($missingCourseIds, JSON_PRETTY_PRINT));
            } else {
                $existingData = json_decode(File::get($filePath), true);
                $mergedData = array_unique(array_merge($existingData, $missingCourseIds));
                File::put($filePath, json_encode($mergedData, JSON_PRETTY_PRINT));
            }
            $this->info("Missing course_ids saved to {$filePath}");
        }

        $this->info("Training course data fetch completed!");
    }
}

