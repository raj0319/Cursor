<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of vehicles
     */
    public function index(Request $request)
    {
        $query = Vehicle::with('vehicleType');

        // Filter by vehicle type
        if ($request->filled('vehicle_type_id')) {
            $query->where('vehicle_type_id', $request->vehicle_type_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by make, model, or license plate
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->orderBy('created_at', 'desc')->paginate(15);
        $vehicleTypes = VehicleType::active()->get();

        return view('admin.vehicles.index', compact('vehicles', 'vehicleTypes'));
    }

    /**
     * Show the form for creating a new vehicle
     */
    public function create()
    {
        $vehicleTypes = VehicleType::active()->get();
        return view('admin.vehicles.create', compact('vehicleTypes'));
    }

    /**
     * Store a newly created vehicle
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'license_plate' => 'required|string|max:255|unique:vehicles',
            'color' => 'required|string|max:255',
            'seats' => 'required|integer|min:1|max:50',
            'features' => 'nullable|string',
            'price_per_day' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:available,booked,maintenance',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('vehicles', 'public');
        }

        // Convert features string to array
        if ($request->filled('features')) {
            $data['features'] = array_map('trim', explode(',', $request->features));
        }

        $data['is_active'] = $request->has('is_active');

        Vehicle::create($data);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle created successfully!');
    }

    /**
     * Display the specified vehicle
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['vehicleType', 'bookings.user']);
        return view('admin.vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified vehicle
     */
    public function edit(Vehicle $vehicle)
    {
        $vehicleTypes = VehicleType::active()->get();
        return view('admin.vehicles.edit', compact('vehicle', 'vehicleTypes'));
    }

    /**
     * Update the specified vehicle
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'license_plate' => 'required|string|max:255|unique:vehicles,license_plate,' . $vehicle->id,
            'color' => 'required|string|max:255',
            'seats' => 'required|integer|min:1|max:50',
            'features' => 'nullable|string',
            'price_per_day' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:available,booked,maintenance',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($vehicle->image) {
                Storage::disk('public')->delete($vehicle->image);
            }
            $data['image'] = $request->file('image')->store('vehicles', 'public');
        }

        // Convert features string to array
        if ($request->filled('features')) {
            $data['features'] = array_map('trim', explode(',', $request->features));
        } else {
            $data['features'] = [];
        }

        $data['is_active'] = $request->has('is_active');

        $vehicle->update($data);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle updated successfully!');
    }

    /**
     * Remove the specified vehicle
     */
    public function destroy(Vehicle $vehicle)
    {
        // Check if vehicle has active bookings
        if ($vehicle->activeBookings()->count() > 0) {
            return redirect()->route('admin.vehicles.index')
                ->with('error', 'Cannot delete vehicle with active bookings!');
        }

        // Delete image
        if ($vehicle->image) {
            Storage::disk('public')->delete($vehicle->image);
        }

        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle deleted successfully!');
    }

    /**
     * Toggle vehicle status
     */
    public function toggleStatus(Vehicle $vehicle)
    {
        $newStatus = $vehicle->status === 'available' ? 'maintenance' : 'available';
        
        // Cannot change status to available if there are active bookings
        if ($newStatus === 'available' && $vehicle->activeBookings()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot set vehicle to available while it has active bookings!');
        }

        $vehicle->update(['status' => $newStatus]);

        return redirect()->back()
            ->with('success', 'Vehicle status updated successfully!');
    }

    /**
     * Toggle vehicle active status
     */
    public function toggleActive(Vehicle $vehicle)
    {
        // Cannot deactivate if there are active bookings
        if ($vehicle->is_active && $vehicle->activeBookings()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot deactivate vehicle with active bookings!');
        }

        $vehicle->update(['is_active' => !$vehicle->is_active]);

        return redirect()->back()
            ->with('success', 'Vehicle status updated successfully!');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'exists:vehicles,id'
        ]);

        $vehicles = Vehicle::whereIn('id', $request->vehicle_ids);

        switch ($request->action) {
            case 'activate':
                $vehicles->update(['is_active' => true]);
                $message = 'Vehicles activated successfully!';
                break;
                
            case 'deactivate':
                // Check for active bookings
                $vehiclesWithBookings = $vehicles->whereHas('activeBookings')->count();
                if ($vehiclesWithBookings > 0) {
                    return redirect()->back()
                        ->with('error', 'Cannot deactivate vehicles with active bookings!');
                }
                $vehicles->update(['is_active' => false]);
                $message = 'Vehicles deactivated successfully!';
                break;
                
            case 'delete':
                // Check for active bookings
                $vehiclesWithBookings = $vehicles->whereHas('activeBookings')->count();
                if ($vehiclesWithBookings > 0) {
                    return redirect()->back()
                        ->with('error', 'Cannot delete vehicles with active bookings!');
                }
                
                // Delete images
                $vehiclesToDelete = $vehicles->get();
                foreach ($vehiclesToDelete as $vehicle) {
                    if ($vehicle->image) {
                        Storage::disk('public')->delete($vehicle->image);
                    }
                }
                
                $vehicles->delete();
                $message = 'Vehicles deleted successfully!';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}