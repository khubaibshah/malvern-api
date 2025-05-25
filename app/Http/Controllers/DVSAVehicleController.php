<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class DVSAVehicleController extends Controller
{


    public function authenticateVes()
    {
        // Check if token is cached
        if (Cache::has('ves_access_token')) {
            return Cache::get('ves_access_token');
        }

        $client = new Client(); // No verify param unless needed


        $response = $client->post('https://login.microsoftonline.com/a455b827-244f-4c97-b5b4-ce5d13b4d00c/oauth2/v2.0/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => env('API_CLIENT_ID'),
                'client_secret' => env('API_CLIENT_SECRET'),
                'scope' => env('SCOPE')
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $accessToken = $data['access_token'];

        // Cache token for 59 minutes (just under the 60-minute expiry to be safe)
        Cache::put('ves_access_token', $accessToken, now()->addMinutes(59));

        return $accessToken;
    }

    public function getMOTTests($registrationNumber)
    {
        // No need to validate $registrationNumber here, as it's part of the URL
        // Create a Guzzle Client instance with SSL certificate verification
        $accessToken = $this->authenticateVes(); // Get the access token from Microsoft

        $client = new Client(); // No verify param unless needed
        try {
            $response = $client->get("https://history.mot.api.gov.uk/v1/trade/vehicles/registration/$registrationNumber", [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                    'X-API-Key' => env('DVSA_API_KEY'),
                    'Accept' => 'application/json',
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                $decodedResponse = json_decode($response->getBody()->getContents(), true);
                return response()->json($decodedResponse, 200, [], JSON_PRETTY_PRINT);
            } else {
                return response()->json(['error' => 'Failed to retrieve vehicle details'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
