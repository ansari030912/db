<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Coupon;

class FetchCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:coupons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch coupons via API and update the local coupons table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // You could store this base URL in your .env if desired: COUPONS_API_URL=https://yourapi.com/v1/coupons
        // For demonstration, using a direct string (replace {{base_url}} with your actual host if needed)
        $apiUrl = 'https://certsgang.com/v1/coupons';

        // Make the API call
        $response = Http::withHeaders([
            'X-API-KEY' => 'b46279cb-13bb-4445-a6f9-6f252b61ae79',
        ])->get($apiUrl);

        if ($response->failed()) {
            // If the request fails, log or print an error message
            $this->error("Failed to fetch coupons from API.");
            return 1; // Non-zero indicates an error
        }

        $couponsData = $response->json(); // This should be an array of coupon data

        // Optionally: Clear out coupons not in the new data (for “fresh” data)
        // 1) Collect all coupon codes from API response
        $incomingCouponCodes = collect($couponsData)->pluck('coupon')->toArray();
        // 2) Delete any coupons in DB that are not returned by the API
        Coupon::whereNotIn('coupon', $incomingCouponCodes)->delete();

        // Now insert or update each coupon
        foreach ($couponsData as $couponItem) {
            // The array is expected in the format:
            // [
            //     "coupon" => "MEGASALE",
            //     "coupon_off" => 40,
            //     "coupon_active" => true
            // ]

            Coupon::updateOrCreate(
                ['coupon' => $couponItem['coupon']], // match on 'coupon' field
                [
                    'coupon_off' => $couponItem['coupon_off'],
                    'coupon_active' => $couponItem['coupon_active']
                ]
            );
        }

        $this->info('Coupons synced successfully!');
        return 0; // Zero indicates success
    }
}
