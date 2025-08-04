<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_number',
        'user_id',
        'vehicle_id',
        'start_date',
        'end_date',
        'total_days',
        'price_per_day',
        'total_amount',
        'status',
        'notes',
        'pickup_location',
        'dropoff_location',
        'confirmed_at',
        'cancelled_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'price_per_day' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->booking_number) {
                $booking->booking_number = self::generateBookingNumber();
            }
            
            // Calculate total days and amount
            $startDate = Carbon::parse($booking->start_date);
            $endDate = Carbon::parse($booking->end_date);
            $booking->total_days = $startDate->diffInDays($endDate) + 1;
            $booking->total_amount = $booking->total_days * $booking->price_per_day;
        });
    }

    /**
     * Generate unique booking number
     */
    public static function generateBookingNumber()
    {
        do {
            $number = 'BK' . date('Y') . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('booking_number', $number)->exists());

        return $number;
    }

    /**
     * Get the user that owns the booking
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vehicle that is booked
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Scope a query to only include active bookings.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Scope a query to only include pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include completed bookings.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include cancelled bookings.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               $this->start_date->isFuture();
    }

    /**
     * Check if booking can be modified
     */
    public function canBeModified()
    {
        return $this->status === 'pending' && $this->start_date->isFuture();
    }

    /**
     * Confirm the booking
     */
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // Update vehicle status
        $this->vehicle->update(['status' => 'booked']);
    }

    /**
     * Cancel the booking
     */
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Update vehicle status back to available if no other active bookings
        if (!$this->vehicle->activeBookings()->where('id', '!=', $this->id)->exists()) {
            $this->vehicle->update(['status' => 'available']);
        }
    }

    /**
     * Complete the booking
     */
    public function complete()
    {
        $this->update(['status' => 'completed']);
        
        // Update vehicle status back to available if no other active bookings
        if (!$this->vehicle->activeBookings()->where('id', '!=', $this->id)->exists()) {
            $this->vehicle->update(['status' => 'available']);
        }
    }

    /**
     * Get the formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return '$' . number_format($this->total_amount, 2);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-primary',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    /**
     * Get booking duration in human readable format
     */
    public function getDurationAttribute()
    {
        return $this->total_days . ' day' . ($this->total_days > 1 ? 's' : '');
    }

    /**
     * Check if booking is active
     */
    public function isActive()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if booking is in progress
     */
    public function isInProgress()
    {
        return $this->status === 'confirmed' && 
               $this->start_date->isPast() && 
               $this->end_date->isFuture();
    }
}