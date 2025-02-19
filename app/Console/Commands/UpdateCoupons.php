<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateCoupons extends Command
{
    // Command signature and description
    protected $signature = 'coupons:update';
    protected $description = 'Fetch coupons from the API and update the database if changes are detected.';

    public function handle()
    {
        $client = new Client();
        $apiUrl = 'https://certsgang.com/v1/coupons';
        $apiKey = 'b46279cb-13bb-4445-a6f9-6f252b61ae79';

        try {
            $response = $client->get($apiUrl, [
                'headers' => [
                    // You can change this header key depending on your API requirements
                    'x-api-key' => $apiKey,
                    'Accept' => 'application/json',
                ],
                'timeout' => 10,
            ]);
        } catch (\Exception $e) {
            $this->error("Failed to fetch coupons: " . $e->getMessage());
            return 1;
        }

        $couponsData = json_decode($response->getBody()->getContents(), true);

        if (!is_array($couponsData)) {
            $this->error('Invalid API response.');
            return 1;
        }

        foreach ($couponsData as $couponData) {
            // Find coupon by unique name
            $coupon = Coupon::where('coupon', $couponData['coupon'])->first();

            if ($coupon) {
                $updates = [];
                if ($coupon->coupon_off != $couponData['coupon_off']) {
                    $updates['coupon_off'] = $couponData['coupon_off'];
                }
                if ($coupon->coupon_active != $couponData['coupon_active']) {
                    $updates['coupon_active'] = $couponData['coupon_active'];
                }
                if (!empty($updates)) {
                    $coupon->update($updates);
                    $this->info("Updated coupon: " . $couponData['coupon']);
                } else {
                    $this->info("No changes for coupon: " . $couponData['coupon']);
                }
            } else {
                Coupon::create($couponData);
                $this->info("Created new coupon: " . $couponData['coupon']);
            }
        }

        $this->info("Coupons update completed.");
        return 0;
    }
}
