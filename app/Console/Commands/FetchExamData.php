<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Exam;
use App\Models\Vendor;
use App\Models\Certificate;
use App\Models\ExamCertificate;

class FetchExamData extends Command
{
    protected $signature = 'exams:update';
    protected $description = 'Fetch exam data from API and update the database';

    public function handle()
    {
        $apiUrl = 'https://certsgang.com/v1/all-exams';

        $response = Http::withHeaders([
            'x-api-key' => 'b46279cb-13bb-4445-a6f9-6f252b61ae79'
        ])->get($apiUrl);

        if ($response->successful()) {
            $data = $response->json();

            foreach ($data as $examData) {
                // Ensure vendor data is valid before inserting
                $vendor = Vendor::updateOrCreate(
                    ['vendor_id' => $examData['vendor_id']],
                    [
                        'vendor_title' => $examData['vendor_title'] ?? 'Unknown Vendor',
                        'vendor_perma' => $examData['vendor_perma'] ?? null,
                    ]
                );

                // Save or update exam based on exam_code to prevent duplicate errors
                // Save or update exam based on exam_id (Don't allow changes to exam_id)
                $exam = Exam::updateOrCreate(
                    ['exam_id' => $examData['exam_id']], // Ensure we match only on exam_id
                    [
                        'exam_code' => $examData['exam_code'],
                        'exam_title' => $examData['exam_title'],
                        'exam_perma' => $examData['exam_perma'],
                        'exam_questions' => $examData['exam_questions'] ?? 0,
                        'vendor_id' => $vendor->vendor_id,
                        'vendor_title' => $examData['vendor_title'],
                        'vendor_perma' => $examData['vendor_perma'],
                    ]
                );


                // Save certifications and link them
                foreach ($examData['exam_certs'] as $certData) {
                    $certification = Certificate::updateOrCreate(
                        ['cert_id' => $certData['cert_id']],
                        [
                            'cert_title' => $certData['cert_title'],
                            'cert_name' => $certData['cert_name'],
                            'cert_perma' => $certData['cert_perma'],
                        ]
                    );

                    // Associate exam with certificate and vendor in pivot table
                    ExamCertificate::updateOrCreate(
                        [
                            'exam_id' => $exam->exam_id,
                            'cert_id' => $certification->cert_id,
                            'vendor_id' => $vendor->vendor_id,
                        ]
                    );
                }
            }

            $this->info('Exam data updated successfully.');
        } else {
            Log::error('Failed to fetch exam data: ' . $response->body());
            $this->error('Failed to fetch exam data. Check logs for details.');
        }
    }
}
