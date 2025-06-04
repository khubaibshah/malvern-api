<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DVSAVehicleController extends Controller
{
    public function authenticateVes()
    {
        if (Cache::has('ves_access_token')) {
            return Cache::get('ves_access_token');
        }

        $client = new Client(['verify' => false]);

        $response = $client->post('https://login.microsoftonline.com/a455b827-244f-4c97-b5b4-ce5d13b4d00c/oauth2/v2.0/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.dvsa.client_id'),
                'client_secret' => config('services.dvsa.client_secret'),
                'scope' => config('services.dvsa.scope'),
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $accessToken = $data['access_token'];

        Cache::put('ves_access_token', $accessToken, now()->addMinutes(59));
        // dd($accessToken);
        return $accessToken;
    }

    public function getMOTTests($registrationNumber)
    {
        Log::info('DVSA API Request Initiated', ['registration' => $registrationNumber]);

        try {
            $accessToken = $this->authenticateVes();
            $apiKey = config('services.dvsa.api_key');

            $client = new Client(['verify' => false]);
            $apiUrl = "https://history.mot.api.gov.uk/v1/trade/vehicles/registration/$registrationNumber";

            Log::debug('Making request to DVSA API', ['url' => $apiUrl]);

            $response = $client->get($apiUrl, [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                    'X-API-Key' => $apiKey,
                    'Accept' => 'application/json',
                ]
            ]);

            $statusCode = $response->getStatusCode();
            Log::info('DVSA API Response', ['status' => $statusCode]);

            if ($statusCode === 200) {
                $decodedResponse = json_decode($response->getBody()->getContents(), true);
                return response()->json($decodedResponse);
            }

            return response()->json(['error' => 'Failed to retrieve vehicle details'], $statusCode);
        } catch (\Exception $e) {
            Log::error('DVSA API Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'registration' => $registrationNumber
            ]);
            return response()->json([
                'error' => 'DVSA API request failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
