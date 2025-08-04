<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display admin dashboard
     */
    public function index()
    {
        // Basic statistics
        $stats = [
            'total_customers' => User::customers()->count(),
            'total_vehicles' => Vehicle::count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Booking::whereIn('status', ['confirmed', 'completed'])->sum('total_amount'),
            'active_bookings' => Booking::active()->count(),
            'pending_bookings' => Booking::pending()->count(),
            'available_vehicles' => Vehicle::available()->count(),
            'vehicle_types' => VehicleType::count(),
        ];

        // Recent bookings
        $recentBookings = Booking::with(['user', 'vehicle'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Monthly revenue chart data
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Booking::whereIn('status', ['confirmed', 'completed'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');
            
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Booking status distribution
        $bookingsByStatus = [
            'pending' => Booking::pending()->count(),
            'confirmed' => Booking::confirmed()->count(),
            'completed' => Booking::completed()->count(),
            'cancelled' => Booking::cancelled()->count(),
        ];

        // Popular vehicle types
        $popularVehicleTypes = VehicleType::withCount(['vehicles as booking_count' => function ($query) {
                $query->join('bookings', 'vehicles.id', '=', 'bookings.vehicle_id')
                      ->whereIn('bookings.status', ['confirmed', 'completed']);
            }])
            ->orderBy('booking_count', 'desc')
            ->take(5)
            ->get();

        // Recent customers
        $recentCustomers = User::customers()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentBookings',
            'monthlyRevenue',
            'bookingsByStatus',
            'popularVehicleTypes',
            'recentCustomers'
        ));
    }

    /**
     * Get dashboard statistics for AJAX
     */
    public function getStats()
    {
        $stats = [
            'total_customers' => User::customers()->count(),
            'total_vehicles' => Vehicle::count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Booking::whereIn('status', ['confirmed', 'completed'])->sum('total_amount'),
            'active_bookings' => Booking::active()->count(),
            'pending_bookings' => Booking::pending()->count(),
            'available_vehicles' => Vehicle::available()->count(),
            'vehicle_types' => VehicleType::count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get monthly revenue data for charts
     */
    public function getMonthlyRevenue()
    {
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Booking::whereIn('status', ['confirmed', 'completed'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');
            
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => (float) $revenue
            ];
        }

        return response()->json($monthlyRevenue);
    }

    /**
     * Get booking status distribution
     */
    public function getBookingStatusDistribution()
    {
        $bookingsByStatus = [
            'Pending' => Booking::pending()->count(),
            'Confirmed' => Booking::confirmed()->count(),
            'Completed' => Booking::completed()->count(),
            'Cancelled' => Booking::cancelled()->count(),
        ];

        return response()->json($bookingsByStatus);
    }
}