<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;
use App\Models\SingleCertificate;
use App\Models\CertificateMultipleExam;
use Illuminate\Support\Facades\Http;

class FetchSingleCertificates extends Command
{
    protected $signature = 'fetch:single-cert';
    protected $description = 'Fetch certificates from the database and update them with API data';

    public function handle()
    {
        $apiKey = 'b46279cb-13bb-4445-a6f9-6f252b61ae79';
        $baseUrl = 'https://certsgang.com/v1/certification/';

        $certificates = Certificate::all();

        foreach ($certificates as $certificate) {
            $certPerma = trim($certificate->cert_perma); // Trim to avoid unexpected spaces

            if (!$certPerma) {
                $this->error("Certificate ID {$certificate->cert_id} has no cert_perma. Skipping.");
                continue;
            }

            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->get("{$baseUrl}{$certPerma}");

            if ($response->successful()) {
                $data = $response->json();

                // Ensure required fields exist, else set default values
                $certId = $data['cert_id'] ?? null;
                if (!$certId) {
                    $this->error("Missing cert_id for {$certPerma}. Skipping.");
                    continue;
                }

                // Save to single_certificates table
                $singleCert = SingleCertificate::updateOrCreate(
                    ['cert_id' => $certId],
                    [
                        'has_multiple_exams' => $data['_has_multiple_exams'] ?? false,
                        'cert_title' => $data['cert_title'] ?? 'Unknown Title',
                        'cert_perma' => $data['cert_perma'] ?? '',
                        'cert_full_name' => $data['cert_full_name'] ?? '',
                        'vendor_title' => $data['vendor_title'] ?? '',
                        'vendor_perma' => $data['vendor_perma'] ?? '',
                        'is_disabled' => $data['is_disabled'] ?? false,
                        'index_tag' => $data['index_tag'] ?? false,
                        'cert_single_exam' => $data['cert_single_exam'] ?? null,
                    ]
                );

                // If certificate has multiple exams, save them
                if (!empty($data['_has_multiple_exams']) && isset($data['cert_multiple_exams'])) {
                    foreach ($data['cert_multiple_exams'] as $exam) {
                        CertificateMultipleExam::updateOrCreate(
                            [
                                'exam_id' => $exam['exam_id'] ?? null,
                                'cert_id' => $certId,
                            ],
                            [
                                'exam_title' => $exam['exam_title'] ?? 'Unknown Exam',
                                'exam_perma' => $exam['exam_perma'] ?? '',
                                'exam_retired' => $exam['exam_retired'] ?? false,
                                'exam_questions' => is_numeric($exam['exam_questions'] ?? null) ? $exam['exam_questions'] : 0,
                                'exam_vendor_title' => $exam['exam_vendor_title'] ?? '',
                                'exam_disabled' => $exam['exam_disabled'] ?? false,
                                'exam_vendor_perma' => $exam['exam_vendor_perma'] ?? '',
                            ]
                        );
                    }
                }

                $this->info("Updated certificate: {$certId}");
            } else {
                $this->error("Failed to fetch data for: {$certPerma}");
            }
        }

        $this->info("All certificates processed.");
    }
}
