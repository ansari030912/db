<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ExamCertificate;

class FetchExamCertificates extends Command
{
    // The name and signature of the console command.
    protected $signature = 'fetch:exam-certificates';

    // The console command description.
    protected $description = 'Fetch exam certificates from API and save to the database';

    public function handle()
    {
        $apiUrl = 'https://certsgang.com/v1/all-exams';
        $apiKey = 'b46279cb-13bb-4445-a6f9-6f252b61ae79';

        // Call the API using Laravel's HTTP client
        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
        ])->get($apiUrl);

        if (!$response->successful()) {
            $this->error('API call failed with status: ' . $response->status());
            return 1;
        }

        $data = $response->json();

        // Loop through each exam
        foreach ($data as $exam) {
            $vendorId = $exam['vendor_id'];
            $examId   = $exam['exam_id'];

            // Check if exam_certs exists and is an array
            if (isset($exam['exam_certs']) && is_array($exam['exam_certs'])) {
                foreach ($exam['exam_certs'] as $cert) {
                    $certId = $cert['cert_id'];

                    // Insert record into exam_certificates table
                    ExamCertificate::create([
                        'vendor_id' => $vendorId,
                        'exam_id'   => $examId,
                        'cert_id'   => $certId,
                    ]);
                }
            }
        }

        $this->info('Data successfully saved to the database.');
        return 0;
    }
}
