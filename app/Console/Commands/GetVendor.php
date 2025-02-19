<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Vendor;

class GetVendor extends Command
{
    protected $signature = 'vendors:update';
    protected $description = 'Update vendors table from external API';

    public function handle()
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => 'b46279cb-13bb-4445-a6f9-6f252b61ae79',
            ])->get('https://certsgang.com/v1/vendors');

            if ($response->failed()) {
                $this->error('Failed to fetch vendors from API.');
                return 1;
            }

            $vendorsFromApi = $response->json();
            $apiVendorIds = collect($vendorsFromApi)->pluck('vendor_id')->toArray();

            foreach ($vendorsFromApi as $apiVendor) {
                Vendor::updateOrCreate(
                    ['vendor_id' => $apiVendor['vendor_id']],
                    [
                        'vendor_title' => $apiVendor['vendor_title'] ?? null,
                        'vendor_perma' => $apiVendor['vendor_perma'] ?? null,
                        'vendor_img' => $apiVendor['vendor_img'] ?? null,
                        'vendor_exams' => $apiVendor['vendor_exams'] ?? 0,
                    ]
                );
            }

            Vendor::whereNotIn('vendor_id', $apiVendorIds)->delete();

            $this->info('Vendors updated successfully.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error updating vendors: ' . $e->getMessage());
            return 1;
        }
    }
}
