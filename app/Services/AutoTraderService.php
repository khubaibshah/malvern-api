<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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
        // Check if token is already cached
        $cachedToken = Cache::get('autotrader_access_token');
        if ($cachedToken) {
            return $cachedToken;
        }
        // Fetch new token from AutoTrader
        $response = Http::post($this->authUrl, [
            'key' => $this->key,
            'secret' => $this->secret,
        ]);
        if ($response->successful()) {
            $token = $response->json('access_token');
            $expiresAt = $response->json('expires_at'); // e.g. 2025-07-24T17:44:40.205Z

            // Calculate seconds until expiry
            $ttl = Carbon::parse($expiresAt)->diffInSeconds(now());

            // Cache token for TTL
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

        if ($response->successful()) {
            return $response->json();
        }

        return ['error' => 'Unable to retrieve vehicle list.'];
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
