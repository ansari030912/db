<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Certificate;

class GetCertificates extends Command
{
    protected $signature = 'certifications:update';
    protected $description = 'Update certifications table from external API';

    public function handle()
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => 'b46279cb-13bb-4445-a6f9-6f252b61ae79',
            ])->get('https://certsgang.com/v1/all-certifications');

            if ($response->failed()) {
                $this->error('Failed to fetch certifications from API.');
                return 1;
            }

            $certsFromApi = $response->json();
            $apiCertIds = collect($certsFromApi)->pluck('cert_id')->toArray();

            foreach ($certsFromApi as $certData) {
                Certificate::updateOrCreate(
                    ['cert_id' => $certData['cert_id']],
                    [
                        'cert_title' => $certData['cert_title'] ?? null,
                        'cert_name' => $certData['cert_name'] ?? null,
                        'cert_perma' => $certData['cert_perma'] ?? null,
                    ]
                );
            }

            Certificate::whereNotIn('cert_id', $apiCertIds)->delete();

            $this->info('Certifications updated successfully.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error updating certifications: ' . $e->getMessage());
            return 1;
        }
    }
}
