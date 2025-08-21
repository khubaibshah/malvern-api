<?php

namespace App\Services;

use App\Jobs\ProcessVehicleImageJob;
use App\Models\ScsCar;
use App\Models\ScsCarImage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AutoTraderService
{
    protected string $authUrl = 'https://api-sandbox.autotrader.co.uk/authenticate';
    protected string $vehicleListUrl = 'https://api-sandbox.autotrader.co.uk/stock?advertiserId=10044000';
    protected string $vehicleUrl = 'https://api-sandbox.autotrader.co.uk/stock?stockId=';

    protected string $key;
    protected string $secret;

    public function __construct()
    {
        $this->key = config('services.autotrader.key');
        $this->secret = config('services.autotrader.secret');
    }

    public function getAuthToken(): ?string
    {
        $cachedToken = Cache::get('autotrader_access_token');
        if ($cachedToken) {
            return $cachedToken;
        }

        $response = Http::post($this->authUrl, [
            'key' => $this->key,
            'secret' => $this->secret,
        ]);

        if ($response->successful()) {
            $token = $response->json('access_token');
            $expiresAt = $response->json('expires_at');

            $ttl = $expiresAt
                ? Carbon::parse($expiresAt)->diffInSeconds(now())
                : 3600;

            Cache::put('autotrader_access_token', $token, $ttl);
            return $token;
        }

        return null;
    }

    public function getVehicleList(): array
    {
        $token = $this->getAuthToken();
        if (!$token) {
            return ['error' => 'Unable to retrieve auth token.'];
        }

        $response = Http::withToken($token)->get($this->vehicleListUrl);

        if (!$response->successful()) {
            return ['error' => 'Unable to retrieve vehicle list.'];
        }

        $data = $response->json();
        $results = $data['results'] ?? null;

        if (!$results || !is_array($results)) {
            logger()->error('AutoTrader response format unexpected', ['response' => $data]);
            return ['error' => 'Unexpected AutoTrader response format.'];
        }

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
                logger()->error('AutoTrader sync failed', ['error' => $e->getMessage()]);
            }
        }

        return [
            'cars' => ScsCar::with(['images' => fn($q) => $q->ordered()])
                ->latest()
                ->get()
                ->map(function ($car) {
                    return [
                        'id' => $car->id,
                        'make' => strtoupper($car->make),
                        'model' => strtoupper($car->model),
                        'year' => $car->year,
                        'description' => $car->description,
                        'registration' => $car->registration,
                        'registration_date' => $car->registration_date,
                        'variant' => $car->variant,
                        'price' => number_format((float) $car->price, 2, '.', ''),
                        'featured' => $car->featured ?? 0,
                        'plus_vat' => $car->plus_vat ?? 0,
                        'vat_qualifying' => $car->vat_qualifying ?? 0,
                        'mileage' => $car->mileage,
                        'fuel_type' => $car->fuel_type,
                        'colour' => $car->colour,
                        'body_style' => $car->body_style,
                        'doors' => $car->doors,
                        'gearbox' => $car->gearbox,
                        'keys' => $car->keys,
                        'engine_size' => $car->engine_size,
                        'veh_type' => $car->veh_type,
                        'vehicle_status' => $car->vehicle_status,
                        'created_at' => $car->created_at,
                        'updated_at' => $car->updated_at,
                        'deleted_at' => $car->deleted_at,
                        'images' => $car->images->pluck('car_image')->toArray(),
                    ];
                })
                ->toArray()
        ];
    }

    public function getSyncedCars()
    {
        $cars = ScsCar::with(['images' => fn($q) => $q->ordered()])
            ->latest()
            ->get()
            ->map(fn($car) => [
                'id' => $car->id,
                'make' => strtoupper($car->make),
                'model' => strtoupper($car->model),
                'year' => $car->year,
                'images' => $car->images->pluck('car_image')->toArray(),
                // ... other fields
            ])
            ->toArray();

        return response()->json([
            'message' => 'Fetched synced vehicles.',
            'data' => $cars,
        ]);
    }



    public function getVehicle($vehicleId): array
    {
        $token = $this->getAuthToken();

        if (!$token) {
            return ['error' => 'Unable to retrieve auth token.'];
        }

        $response = Http::withToken($token)->get("{$this->vehicleUrl}{$vehicleId}");

        if ($response->successful()) {
            return $response->json();
        }

        return ['error' => 'Unable to retrieve vehicle.'];
    }
}
