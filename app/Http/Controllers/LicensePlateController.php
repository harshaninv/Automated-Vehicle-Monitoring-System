<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class LicensePlateController extends Controller
{
    /**
     * Format a license plate number to match the Sri Lankan format
     * Pattern: XX YYY 1234 (where XX = 1-2 letters, YYY = 1-3 letters, 1234 = 4 digits)
     *
     * @param string $plateNumber The raw plate number from API
     * @return string The formatted plate number
     */
    private function formatLicensePlate(string $plateNumber): string
    {
        // Remove any spaces, dashes, or other non-alphanumeric characters
        $plateNumber = preg_replace('/[^A-Z0-9]/i', '', strtoupper($plateNumber));

        // Check if the plate number has enough characters to format
        if (strlen($plateNumber) < 3) {
            return $plateNumber; // Return as is if too short
        }

        // Determine the parts of the license plate
        // We need to extract letters and numbers
        preg_match('/^([A-Z]+)([0-9]+)$/', $plateNumber, $matches);

        if (count($matches) >= 3) {
            $letters = $matches[1];
            $numbers = $matches[2];

            // Format the letters part (XX YYY)
            $lettersLength = strlen($letters);
            if ($lettersLength <= 2) {
                // If only 1-2 letters, we treat as the first part
                $firstPart = $letters;
                $secondPart = '';
            } else {
                // If 3+ letters, split into two parts
                $firstPart = substr($letters, 0, min(2, $lettersLength));
                $secondPart = substr($letters, min(2, $lettersLength));
            }

            // Format the numbers part, ensuring it's 4 digits
            if (strlen($numbers) > 4) {
                $numbers = substr($numbers, 0, 4);
            } elseif (strlen($numbers) < 4) {
                $numbers = str_pad($numbers, 4, '0', STR_PAD_LEFT);
            }

            // Build the formatted plate number
            $formattedPlate = trim($firstPart);
            if (!empty($secondPart)) {
                $formattedPlate .= ' ' . trim($secondPart);
            }
            $formattedPlate .= ' ' . $numbers;

            return $formattedPlate;
        }

        // If we couldn't parse the format, return the cleaned up plate number
        return $plateNumber;
    }
    public function scan(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:10240', // Max 10MB
        ]);

        $apiKey = Config::get('services.plate-recognition.api_key');
        $apiUrl = Config::get('services.plate-recognition.api_url');

        if (empty($apiKey) || empty($apiUrl)) {
            return response()->json([
                'error' => 'Plate recognition API configuration is missing'
            ], 500);
        }

        try {
            // Get the image file
            $image = $request->file('upload');

            // Create multipart request to the API with 'lk' (Sri Lanka) region parameter
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $apiKey
            ])->attach(
                'upload', file_get_contents($image), $image->getClientOriginalName()
            )->post($apiUrl, [
                'regions' => 'lk'  // Set region to Sri Lanka
            ]);

            // Check for API errors
            if (!$response->successful()) {
                return response()->json([
                    'error' => 'API error: ' . $response->status() . ' ' . $response->body()
                ], 500);
            }

            // Get the API response
            $apiResponse = $response->json();

            // Format the plate number if results exist
            if (!empty($apiResponse['results']) && count($apiResponse['results']) > 0) {
                $plateNumber = $apiResponse['results'][0]['plate'];
                $apiResponse['results'][0]['plate'] = $this->formatLicensePlate($plateNumber);
            }

            // Return the modified response
            return $apiResponse;

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error processing request: ' . $e->getMessage()
            ], 500);
        }
    }
}
