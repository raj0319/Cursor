<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use App\Models\Vehicle;
use App\Models\Booking;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application homepage
     */
    public function index()
    {
        $vehicleTypes = VehicleType::active()
            ->with(['availableVehicles'])
            ->take(6)
            ->get();

        $featuredVehicles = Vehicle::active()
            ->available()
            ->with('vehicleType')
            ->take(8)
            ->get();

        $stats = [
            'total_vehicles' => Vehicle::active()->count(),
            'total_bookings' => Booking::count(),
            'happy_customers' => Booking::completed()->distinct('user_id')->count(),
            'vehicle_types' => VehicleType::active()->count(),
        ];

        return view('welcome', compact('vehicleTypes', 'featuredVehicles', 'stats'));
    }

    /**
     * Show vehicle types page
     */
    public function vehicleTypes()
    {
        $vehicleTypes = VehicleType::active()
            ->with(['availableVehicles'])
            ->paginate(12);

        return view('vehicle-types', compact('vehicleTypes'));
    }

    /**
     * Show vehicles by type
     */
    public function vehiclesByType(VehicleType $vehicleType)
    {
        $vehicles = $vehicleType->availableVehicles()
            ->with('vehicleType')
            ->paginate(12);

        return view('vehicles', compact('vehicleType', 'vehicles'));
    }

    /**
     * Show single vehicle details
     */
    public function vehicleDetails(Vehicle $vehicle)
    {
        $vehicle->load('vehicleType');
        
        $relatedVehicles = Vehicle::where('vehicle_type_id', $vehicle->vehicle_type_id)
            ->where('id', '!=', $vehicle->id)
            ->available()
            ->take(4)
            ->get();

        return view('vehicle-details', compact('vehicle', 'relatedVehicles'));
    }

    /**
     * Search vehicles
     */
    public function search(Request $request)
    {
        $query = Vehicle::query()->active()->available()->with('vehicleType');

        // Filter by vehicle type
        if ($request->filled('vehicle_type_id')) {
            $query->where('vehicle_type_id', $request->vehicle_type_id);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        // Filter by seats
        if ($request->filled('seats')) {
            $query->where('seats', '>=', $request->seats);
        }

        // Filter by availability dates
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $query->whereDoesntHave('bookings', function ($q) use ($startDate, $endDate) {
                $q->whereIn('status', ['pending', 'confirmed'])
                  ->where(function ($query) use ($startDate, $endDate) {
                      $query->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function ($q) use ($startDate, $endDate) {
                              $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                          });
                  });
            });
        }

        // Search by make, model, or features
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('make', 'like', "%{$searchTerm}%")
                  ->orWhere('model', 'like', "%{$searchTerm}%")
                  ->orWhere('features', 'like', "%{$searchTerm}%");
            });
        }

        $vehicles = $query->paginate(12)->withQueryString();
        $vehicleTypes = VehicleType::active()->get();

        return view('search-results', compact('vehicles', 'vehicleTypes'));
    }
}