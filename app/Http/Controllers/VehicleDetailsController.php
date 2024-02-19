<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class VehicleDetailsController extends Controller
{
    public function getVehicleDetails(Request $request)
    {
        // Validate the request
        $request->validate([
            'registrationNumber' => 'required|string|max:10', // Adjust the max length according to your needs
        ]);
        
        // Create a Guzzle Client instance with SSL certificate verification
        $client = new Client([
            'verify' => 'C:\Users\khuba\projects\malvern-api\storage\certificates\cacert.pem', // Specify the path to your CA certificate bundle
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
                return $response->getBody()->getContents();
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

