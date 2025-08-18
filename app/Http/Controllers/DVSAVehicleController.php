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

        return $accessToken;
    }

    public function getMOTTests($registrationNumber)
    {

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

    public function getMOTTestsFiltered($registrationNumber)
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

                // If it's an array of vehicles 
                $vehicle = is_array($decodedResponse) && isset($decodedResponse[0])
                    ? $decodedResponse[0]
                    : $decodedResponse;
                $latestMot = $decodedResponse['motTests'][0] ?? null;

                $filtered = [
                    'registration' => $decodedResponse['registration'] ?? null,
                    'make' => $decodedResponse['make'] ?? null,
                    'model' => $decodedResponse['model'] ?? null,
                    'firstUsedDate' => $decodedResponse['firstUsedDate'] ?? null,
                    'fuelType' => $decodedResponse['fuelType'] ?? null,
                    'primaryColour' => $decodedResponse['primaryColour'] ?? null,
                    'registrationDate' => $decodedResponse['registrationDate'] ?? null,
                    'manufactureDate' => $decodedResponse['manufactureDate'] ?? null,
                    'engineSize' => $decodedResponse['engineSize'] ?? null,
                    'odometerValue'     => $latestMot['odometerValue'] ?? null,
                    'odometerUnit'      => $latestMot['odometerUnit'] ?? null,
                ];
                return response()->json($filtered, 200);
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

    public function vesVehicleDetails(Request $request)
    {
        // Validate the request
        $request->validate([
            'registrationNumber' => 'required|string|max:10', // Adjust the max length according to your needs
        ]);
        
        // Create a Guzzle Client instance with SSL certificate verification
        $client = new Client([
            'verify' => env('SSL_URL'), // path to CA certificate bundle
        ]);

        // Make a request to the DVLA API using Guzzle Client
        try {
            $response = $client->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
                'headers' => [
                    'x-api-key' => env('VES_API_KEY'),
                ],
                'json' => [
                    'registrationNumber' => $request->registrationNumber,
                ],
            ]);

            // Check if the request was successful
            if ($response->getStatusCode() === 200) {
                // Return the response from the DVLA API
                $decodedResponse = json_decode($response->getBody()->getContents(), true);
                return response()->json($decodedResponse, $response->getStatusCode(), [], JSON_PRETTY_PRINT);
            } else {
                // Return an error response if the request failed
                return response()->json(['error' => 'Failed to retrieve vehicle details'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
