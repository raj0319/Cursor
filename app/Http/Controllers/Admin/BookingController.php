<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of bookings
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'vehicle.vehicleType']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        // Search by booking number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'vehicle.vehicleType']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Confirm a booking
     */
    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending bookings can be confirmed!');
        }

        $booking->confirm();

        return redirect()->back()
            ->with('success', 'Booking confirmed successfully!');
    }

    /**
     * Complete a booking
     */
    public function complete(Booking $booking)
    {
        if ($booking->status !== 'confirmed') {
            return redirect()->back()
                ->with('error', 'Only confirmed bookings can be completed!');
        }

        $booking->complete();

        return redirect()->back()
            ->with('success', 'Booking completed successfully!');
    }

    /**
     * Cancel a booking
     */
    public function cancel(Booking $booking)
    {
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return redirect()->back()
                ->with('error', 'This booking cannot be cancelled!');
        }

        $booking->cancel();

        return redirect()->back()
            ->with('success', 'Booking cancelled successfully!');
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);

        $newStatus = $request->status;
        $currentStatus = $booking->status;

        // Validate status transitions
        $validTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => []
        ];

        if (!in_array($newStatus, $validTransitions[$currentStatus])) {
            return redirect()->back()
                ->with('error', 'Invalid status transition!');
        }

        // Update booking status
        switch ($newStatus) {
            case 'confirmed':
                $booking->confirm();
                break;
            case 'completed':
                $booking->complete();
                break;
            case 'cancelled':
                $booking->cancel();
                break;
        }

        return redirect()->back()
            ->with('success', 'Booking status updated successfully!');
    }

    /**
     * Bulk actions for bookings
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:confirm,cancel,delete',
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id'
        ]);

        $bookings = Booking::whereIn('id', $request->booking_ids)->get();

        switch ($request->action) {
            case 'confirm':
                $count = 0;
                foreach ($bookings as $booking) {
                    if ($booking->status === 'pending') {
                        $booking->confirm();
                        $count++;
                    }
                }
                $message = "{$count} bookings confirmed successfully!";
                break;
                
            case 'cancel':
                $count = 0;
                foreach ($bookings as $booking) {
                    if (in_array($booking->status, ['pending', 'confirmed'])) {
                        $booking->cancel();
                        $count++;
                    }
                }
                $message = "{$count} bookings cancelled successfully!";
                break;
                
            case 'delete':
                // Only allow deletion of cancelled bookings
                $deletableBookings = $bookings->where('status', 'cancelled');
                $count = $deletableBookings->count();
                
                if ($count === 0) {
                    return redirect()->back()
                        ->with('error', 'Only cancelled bookings can be deleted!');
                }
                
                foreach ($deletableBookings as $booking) {
                    $booking->delete();
                }
                $message = "{$count} bookings deleted successfully!";
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export bookings to CSV
     */
    public function export(Request $request)
    {
        $query = Booking::with(['user', 'vehicle.vehicleType']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $filename = 'bookings_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Booking Number',
                'Customer Name',
                'Customer Email',
                'Vehicle',
                'Start Date',
                'End Date',
                'Total Days',
                'Price Per Day',
                'Total Amount',
                'Status',
                'Created At'
            ]);

            // CSV data
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_number,
                    $booking->user->name,
                    $booking->user->email,
                    $booking->vehicle->full_name,
                    $booking->start_date->format('Y-m-d'),
                    $booking->end_date->format('Y-m-d'),
                    $booking->total_days,
                    $booking->price_per_day,
                    $booking->total_amount,
                    ucfirst($booking->status),
                    $booking->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}