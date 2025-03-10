<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\UnlimitedAccess;

class FetchUnlimitedAccessData extends Command
{
    protected $signature = 'fetch:unlimited-access';
    protected $description = 'Fetch Unlimited Access data from the API and store it in the database';

    public function handle()
    {
        $apiUrl = 'https://certsgang.com/v1/unlimited_access';
        $apiKey = 'b46279cb-13bb-4445-a6f9-6f252b61ae79';

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Accept' => 'application/json',
        ])->get($apiUrl);

        if ($response->successful()) {
            $data = $response->json();

            UnlimitedAccess::create([
                'pdf_full_price' => $data['pdf_full_price'],
                'pdf_price' => $data['pdf_price'],
                'pdf_cart' => $data['pdf_cart'],
                'te_full_price' => $data['te_full_price'],
                'te_price' => $data['te_price'],
                'te_cart' => $data['te_cart'],
            ]);

            $this->info('Unlimited access data successfully fetched and stored.');
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
