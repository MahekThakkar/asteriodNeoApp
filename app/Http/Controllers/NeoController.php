<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NeoController extends Controller
{
    public function index()
    {
        return view('neo.index'); // Our Form
    }


    // Handle form submission and fetch stats from NASA's API
    public function fetchStats(Request $request)
    {
        // Validate the input dates
        $request->validate([
            'start_date' => 'required|date|before_or_equal:end_date', // Start date must be before or equal to end date
            'end_date' => 'required|date|after_or_equal:start_date', // End date must be after or equal to start date
        ]);

        // Retrieve input data from the form
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $apiKey = env('NASA_API_KEY', 'DEMO_KEY'); // Use NASA API key from environment or fallback to DEMO_KEY

        // Fetch data from NASA API
        $response = Http::get("https://api.nasa.gov/neo/rest/v1/feed", [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'api_key' => $apiKey,
        ]);
        
        // Check if the API request failed
        if ($response->failed()) {
            return back()->with('error', 'Failed to fetch data from NASA API.');
        }

        // Parse the API response JSON data
        $data = $response->json()['near_earth_objects'];

        // Process stats
        $fastestAsteroid = null; // To store details of the fastest asteroid
        $closestAsteroid = null; // To store details of the closest asteroid
        $totalAsteroidsPerDay = []; // To store the count of asteroids per day
        $averageSizes = []; // To store asteroid sizes
        $totalSize = 0; // Sum of all asteroid sizes
        $count = 0; // Total number of asteroids


        // Process the API data for each date
        foreach ($data as $date => $asteroids) {
            $totalAsteroidsPerDay[$date] = count($asteroids); // Count asteroids for the current date

            foreach ($asteroids as $asteroid) {
                // Extract speed and distance from asteroid data
                $speed = $asteroid['close_approach_data'][0]['relative_velocity']['kilometers_per_hour'];
                $distance = $asteroid['close_approach_data'][0]['miss_distance']['kilometers'];

                // Calculate the average size of the asteroid
                $size = ($asteroid['estimated_diameter']['kilometers']['estimated_diameter_min'] +
                         $asteroid['estimated_diameter']['kilometers']['estimated_diameter_max']) / 2;

                $totalSize += $size; // Add size to total
                $count++; // Increment asteroid count

                // Find the fastest asteroid
                if (!$fastestAsteroid || $speed > $fastestAsteroid['speed']) {
                    $fastestAsteroid = [
                        'id' => $asteroid['id'],
                        'speed' => $speed,
                    ];
                }

                // Find the closest asteroid
                if (!$closestAsteroid || $distance < $closestAsteroid['distance']) {
                    $closestAsteroid = [
                        'id' => $asteroid['id'],
                        'distance' => $distance,
                    ];
                }
            }
        }

        // Calculate the average size of all asteroids
        $averageSize = $count > 0 ? $totalSize / $count : 0;

        return view('neo.result', [
            'fastestAsteroid' => $fastestAsteroid,
            'closestAsteroid' => $closestAsteroid,
            'averageSize' => $averageSize,
            'totalAsteroidsPerDay' => $totalAsteroidsPerDay,
        ]);
    }
}
