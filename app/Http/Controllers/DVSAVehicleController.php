<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class DVSAVehicleController extends Controller
{
    public function getMOTTests(Request $request, $registrationNumber)
{
    // No need to validate $registrationNumber here, as it's part of the URL
    // Create a Guzzle Client instance with SSL certificate verification
    $client = new Client([
        'verify' => env('SSL_URL'), // Specify the path to your CA certificate bundle
    ]);
    
    // Make the request to the DVSA API
    $response = $client->get("https://beta.check-mot.service.gov.uk/trade/vehicles/mot-tests?registration=" . $registrationNumber, [
        'headers' => [
            'x-api-key' => env('DVSA_API_KEY'),
            'Accept' => 'application/json+v6'
        ],
    ]);
    
    // Check if the request was successful
    if ($response->getStatusCode() == 200) {
        $data = $response->getBody()->getContents();
        return json_decode($data, true); // Return JSON response from the API
    } else {
        // Handle errors
        return response()->json(['error' => 'Failed to fetch MOT tests.'], $response->getStatusCode());
    }
}

}
