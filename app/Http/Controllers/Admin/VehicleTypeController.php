<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of vehicle types
     */
    public function index()
    {
        $vehicleTypes = VehicleType::withCount('vehicles')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.vehicle-types.index', compact('vehicleTypes'));
    }

    /**
     * Show the form for creating a new vehicle type
     */
    public function create()
    {
        return view('admin.vehicle-types.create');
    }

    /**
     * Store a newly created vehicle type
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_types',
            'description' => 'nullable|string',
            'base_price_per_day' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('vehicle-types', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        VehicleType::create($data);

        return redirect()->route('admin.vehicle-types.index')
            ->with('success', 'Vehicle type created successfully!');
    }

    /**
     * Display the specified vehicle type
     */
    public function show(VehicleType $vehicleType)
    {
        $vehicleType->load(['vehicles' => function ($query) {
            $query->with('bookings')->orderBy('created_at', 'desc');
        }]);

        return view('admin.vehicle-types.show', compact('vehicleType'));
    }

    /**
     * Show the form for editing the specified vehicle type
     */
    public function edit(VehicleType $vehicleType)
    {
        return view('admin.vehicle-types.edit', compact('vehicleType'));
    }

    /**
     * Update the specified vehicle type
     */
    public function update(Request $request, VehicleType $vehicleType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vehicle_types,name,' . $vehicleType->id,
            'description' => 'nullable|string',
            'base_price_per_day' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($vehicleType->image) {
                Storage::disk('public')->delete($vehicleType->image);
            }
            $data['image'] = $request->file('image')->store('vehicle-types', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        $vehicleType->update($data);

        return redirect()->route('admin.vehicle-types.index')
            ->with('success', 'Vehicle type updated successfully!');
    }

    /**
     * Remove the specified vehicle type
     */
    public function destroy(VehicleType $vehicleType)
    {
        // Check if vehicle type has vehicles
        if ($vehicleType->vehicles()->count() > 0) {
            return redirect()->route('admin.vehicle-types.index')
                ->with('error', 'Cannot delete vehicle type that has vehicles assigned to it!');
        }

        // Delete image
        if ($vehicleType->image) {
            Storage::disk('public')->delete($vehicleType->image);
        }

        $vehicleType->delete();

        return redirect()->route('admin.vehicle-types.index')
            ->with('success', 'Vehicle type deleted successfully!');
    }

    /**
     * Toggle vehicle type active status
     */
    public function toggleActive(VehicleType $vehicleType)
    {
        // Check if deactivating and has active vehicles with bookings
        if ($vehicleType->is_active) {
            $activeVehiclesWithBookings = $vehicleType->vehicles()
                ->whereHas('activeBookings')
                ->count();
            
            if ($activeVehiclesWithBookings > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot deactivate vehicle type with vehicles that have active bookings!');
            }
        }

        $vehicleType->update(['is_active' => !$vehicleType->is_active]);

        // If deactivating, also deactivate all vehicles of this type
        if (!$vehicleType->is_active) {
            $vehicleType->vehicles()->update(['is_active' => false]);
        }

        return redirect()->back()
            ->with('success', 'Vehicle type status updated successfully!');
    }
}