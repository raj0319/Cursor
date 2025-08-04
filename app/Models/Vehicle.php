<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_type_id',
        'make',
        'model',
        'year',
        'license_plate',
        'color',
        'seats',
        'features',
        'price_per_day',
        'image',
        'status',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'is_active' => 'boolean',
            'features' => 'array',
        ];
    }

    /**
     * Get the vehicle type that owns the vehicle
     */
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * Get all bookings for this vehicle
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get active bookings for this vehicle
     */
    public function activeBookings()
    {
        return $this->hasMany(Booking::class)->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Scope a query to only include active vehicles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include available vehicles.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    /**
     * Check if vehicle is available for given date range
     */
    public function isAvailableForDates($startDate, $endDate)
    {
        if ($this->status !== 'available' || !$this->is_active) {
            return false;
        }

        $conflictingBookings = $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            })
            ->count();

        return $conflictingBookings === 0;
    }

    /**
     * Get the full vehicle name
     */
    public function getFullNameAttribute()
    {
        return "{$this->year} {$this->make} {$this->model}";
    }

    /**
     * Get the formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price_per_day, 2);
    }

    /**
     * Get the image URL or default placeholder
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-vehicle.jpg');
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'available' => 'badge-success',
            'booked' => 'badge-warning',
            'maintenance' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    /**
     * Get features as formatted string
     */
    public function getFeaturesStringAttribute()
    {
        if (is_array($this->features)) {
            return implode(', ', $this->features);
        }
        return $this->features ?? '';
    }
}