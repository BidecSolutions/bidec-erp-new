<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyLocations;
use App\Repositories\LocationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\LocationRepositoryInterface;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    //
    private $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }


    public function detail(Request $request)    {
        // Get the logged-in user
        $user = $request->user(); // Assumes the user is authenticated and passed via middleware

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
                'data' => null
            ], 401); // Unauthorized
        }

        // Extract campus IDs from the user's data
        $campusIdsArray = json_decode($user->company_location_ids_array, true);

        if (empty($campusIdsArray)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No campuses associated with this user',
                'data' => null
            ], 404); // Not Found
        }

        // Extract the campus IDs into a simple array
        $campusIds = array_column($campusIdsArray, 'company_location_id');

        // Fetch campus details from the database
        $locations = CompanyLocations::whereIn('id', $campusIds)->get();

        // Check if any campus information was retrieved
        if ($locations->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No location information found',
                'data' => null
            ], 404); // Not Found
        }

        // Return campus information
        return response()->json([
            'status' => 'success',
            'message' => 'Campus information retrieved successfully',
            'data' => $locations
        ], 200); // OK
    }

}
