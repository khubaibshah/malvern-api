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
        Log::info('Starting SyncAutoTraderVehiclesJob...');

        try {
            $token = $autoTraderService->getAuthToken();
            if (!$token) {
                Log::error('No AutoTrader token');
                return;
            }

            $response = Http::withToken($token)->get($autoTraderService->getVehicleListUrl());

            if (!$response->successful()) {
                Log::error('Failed to fetch AutoTrader list', ['response' => $response->body()]);
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
                        [/* same data here */]
                    );

                    Log::info("AutoTrader vehicle synced: {$scsCar->registration}");

                    foreach ($media as $index => $image) {
                        ProcessVehicleImageJob::dispatch($scsCar->id, $image, $index);
                    }

                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    Log::error('AutoTrader vehicle DB sync failed', [
                        'error' => $e->getMessage(),
                        'vehicle' => $item['vehicle']['registration'] ?? 'unknown',
                    ]);
                }
            }

            Log::info('AutoTrader sync completed');
        } catch (\Throwable $e) {
            Log::error('SyncAutoTraderVehiclesJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
