<?php

namespace App\Jobs;

use App\Models\ScsCar;
use App\Jobs\ProcessVehicleImageJob;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\AutoTraderService;

class SyncAutoTraderVehiclesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // protected string $authUrl = 'https://api-sandbox.autotrader.co.uk/authenticate';
    // protected string $vehicleListUrl = 'https://api-sandbox.autotrader.co.uk/stock?advertiserId=10044000';
    // protected string $vehicleUrl = 'https://api-sandbox.autotrader.co.uk/stock?stockId=';

    // protected string $key;
    // protected string $secret;

    public function handle(AutoTraderService $autoTraderService): void
    {
        $token = $autoTraderService->getAuthToken();
        if (!$token) {
            Log::error('No AutoTrader token');
            return;
        }

        $response = Http::withToken($token)->get($autoTraderService->getVehicleListUrl());

        if (!$response->successful()) {
            Log::error('Failed to fetch AutoTrader list');
            return;
        }

        $data = $response->json();
        $results = $data['results'] ?? [];

        foreach ($results as $item) {
            DB::beginTransaction();
            try {
                $vehicle = $item['vehicle'];
                $advert = $item['adverts']['retailAdverts'] ?? [];
                $media = $item['media']['images'] ?? [];

                $scsCar = ScsCar::updateOrCreate(
                    ['registration' => $vehicle['registration']],
                    [
                        'make' => strtoupper($vehicle['make']),
                        'model' => strtoupper($vehicle['model']),
                        'year' => $vehicle['yearOfManufacture'],
                        'registration_date' => $vehicle['firstRegistrationDate'],
                        'variant' => $vehicle['derivative'],
                        'price' => $advert['totalPrice']['amountGBP'] ?? 0,
                        'featured' => 0,
                        'plus_vat' => 0,
                        'mileage' => $vehicle['odometerReadingMiles'],
                        'fuel_type' => $vehicle['fuelType'],
                        'colour' => $vehicle['colour'],
                        'body_style' => $vehicle['bodyType'],
                        'doors' => $vehicle['doors'],
                        'gearbox' => $vehicle['transmissionType'],
                        'keys' => null,
                        'engine_size' => $vehicle['engineCapacityCC'],
                        'veh_type' => $vehicle['vehicleType'],
                        'vehicle_status' => $item['metadata']['lifecycleState'] ?? null,
                        'description' => $advert['description2'] ?? $advert['description'] ?? 'SCS Car Sales Limited is proud to present this vehicle.'
                    ]
                );

                foreach ($media as $index => $image) {
                    ProcessVehicleImageJob::dispatch($scsCar->id, $image, $index);
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('AutoTrader sync failed', ['error' => $e->getMessage()]);
            }
        }

        Log::info('AutoTrader sync completed');
    }
}
