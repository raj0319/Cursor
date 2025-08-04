<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display customer's bookings
     */
    public function index()
    {
        $bookings = Auth::user()->bookings()
            ->with(['vehicle.vehicleType'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking
     */
    public function create(Vehicle $vehicle)
    {
        if (!$vehicle->is_active || $vehicle->status !== 'available') {
            return redirect()->back()->with('error', 'This vehicle is not available for booking.');
        }

        return view('customer.bookings.create', compact('vehicle'));
    }

    /**
     * Store a newly created booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_location' => 'nullable|string|max:255',
            'dropoff_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        // Check if vehicle is available for the selected dates
        if (!$vehicle->isAvailableForDates($request->start_date, $request->end_date)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Vehicle is not available for the selected dates.');
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $vehicle->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'price_per_day' => $vehicle->price_per_day,
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'notes' => $request->notes,
        ]);

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Booking created successfully! Booking number: ' . $booking->booking_number);
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['vehicle.vehicleType']);

        return view('customer.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking
     */
    public function edit(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if booking can be modified
        if (!$booking->canBeModified()) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'This booking cannot be modified.');
        }

        $booking->load(['vehicle.vehicleType']);

        return view('customer.bookings.edit', compact('booking'));
    }

    /**
     * Update the specified booking
     */
    public function update(Request $request, Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if booking can be modified
        if (!$booking->canBeModified()) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'This booking cannot be modified.');
        }

        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_location' => 'nullable|string|max:255',
            'dropoff_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if vehicle is available for the new dates (excluding current booking)
        $vehicle = $booking->vehicle;
        $conflictingBookings = $vehicle->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('id', '!=', $booking->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                          ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->count();

        if ($conflictingBookings > 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Vehicle is not available for the selected dates.');
        }

        // Recalculate total days and amount
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalAmount = $totalDays * $booking->price_per_day;

        $booking->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'total_amount' => $totalAmount,
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'notes' => $request->notes,
        ]);

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Booking updated successfully!');
    }

    /**
     * Cancel the specified booking
     */
    public function cancel(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if booking can be cancelled
        if (!$booking->canBeCancelled()) {
            return redirect()->route('customer.bookings.show', $booking)
                ->with('error', 'This booking cannot be cancelled.');
        }

        $booking->cancel();

        return redirect()->route('customer.bookings.index')
            ->with('success', 'Booking cancelled successfully!');
    }

    /**
     * Get booking form for AJAX
     */
    public function getBookingForm(Vehicle $vehicle)
    {
        if (!$vehicle->is_active || $vehicle->status !== 'available') {
            return response()->json(['error' => 'Vehicle not available'], 400);
        }

        return response()->json([
            'vehicle' => $vehicle->load('vehicleType'),
            'html' => view('customer.bookings.form', compact('vehicle'))->render()
        ]);
    }

    /**
     * Check vehicle availability for dates
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $isAvailable = $vehicle->isAvailableForDates($request->start_date, $request->end_date);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalAmount = $totalDays * $vehicle->price_per_day;

        return response()->json([
            'available' => $isAvailable,
            'total_days' => $totalDays,
            'price_per_day' => $vehicle->price_per_day,
            'total_amount' => $totalAmount,
            'formatted_total' => '$' . number_format($totalAmount, 2)
        ]);
    }
}