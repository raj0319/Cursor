<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\VehicleTypeController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/vehicle-types', [HomeController::class, 'vehicleTypes'])->name('vehicle-types');
Route::get('/vehicle-types/{vehicleType}', [HomeController::class, 'vehiclesByType'])->name('vehicles.by-type');
Route::get('/vehicles/{vehicle}', [HomeController::class, 'vehicleDetails'])->name('vehicle.details');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Authentication routes (these would typically be handled by Laravel Breeze/Jetstream)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        auth()->logout();
        return redirect('/');
    })->name('logout');
    
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
});

// Customer routes (authenticated users)
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::resource('bookings', CustomerBookingController::class);
    Route::post('bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('vehicles/{vehicle}/book', [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::post('check-availability', [CustomerBookingController::class, 'checkAvailability'])->name('check-availability');
});

// Admin routes (authenticated admin users)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/monthly-revenue', [DashboardController::class, 'getMonthlyRevenue'])->name('dashboard.monthly-revenue');
    Route::get('/dashboard/booking-status', [DashboardController::class, 'getBookingStatusDistribution'])->name('dashboard.booking-status');
    
    // Vehicle Types Management
    Route::resource('vehicle-types', VehicleTypeController::class);
    Route::post('vehicle-types/{vehicleType}/toggle-active', [VehicleTypeController::class, 'toggleActive'])->name('vehicle-types.toggle-active');
    
    // Vehicles Management
    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/{vehicle}/toggle-status', [VehicleController::class, 'toggleStatus'])->name('vehicles.toggle-status');
    Route::post('vehicles/{vehicle}/toggle-active', [VehicleController::class, 'toggleActive'])->name('vehicles.toggle-active');
    Route::post('vehicles/bulk-action', [VehicleController::class, 'bulkAction'])->name('vehicles.bulk-action');
    
    // Bookings Management
    Route::resource('bookings', AdminBookingController::class)->only(['index', 'show']);
    Route::post('bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
    Route::post('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('bookings/{booking}/update-status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::post('bookings/bulk-action', [AdminBookingController::class, 'bulkAction'])->name('bookings.bulk-action');
    Route::get('bookings/export', [AdminBookingController::class, 'export'])->name('bookings.export');
});

// API routes for AJAX requests
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::post('check-availability', [CustomerBookingController::class, 'checkAvailability']);
    Route::get('vehicles/{vehicle}/booking-form', [CustomerBookingController::class, 'getBookingForm']);
});

// Fallback route
Route::fallback(function () {
    return view('errors.404');
});