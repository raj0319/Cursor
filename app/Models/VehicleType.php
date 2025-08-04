<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'base_price_per_day',
        'image',
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
            'base_price_per_day' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all vehicles of this type
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get available vehicles of this type
     */
    public function availableVehicles()
    {
        return $this->hasMany(Vehicle::class)->where('status', 'available')->where('is_active', true);
    }

    /**
     * Scope a query to only include active vehicle types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->base_price_per_day, 2);
    }

    /**
     * Get the image URL or default placeholder
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-vehicle-type.jpg');
    }
}