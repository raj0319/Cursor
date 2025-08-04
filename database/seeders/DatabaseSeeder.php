<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\VehicleType;
use App\Models\Vehicle;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@vehiclerent.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567890',
            'address' => '123 Admin Street, Admin City',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create customer users
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567891',
                'address' => '456 Customer Lane, Customer City',
                'role' => 'customer',
                'is_active' => true,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567892',
                'address' => '789 User Avenue, User Town',
                'role' => 'customer',
                'is_active' => true,
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567893',
                'address' => '321 Client Road, Client Village',
                'role' => 'customer',
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customerData) {
            User::create($customerData);
        }

        // Create vehicle types
        $vehicleTypes = [
            [
                'name' => 'Economy Car',
                'description' => 'Affordable and fuel-efficient cars perfect for city driving and short trips.',
                'base_price_per_day' => 25.00,
                'is_active' => true,
            ],
            [
                'name' => 'Compact Car',
                'description' => 'Small and efficient vehicles ideal for urban commuting and parking in tight spaces.',
                'base_price_per_day' => 30.00,
                'is_active' => true,
            ],
            [
                'name' => 'Mid-size Car',
                'description' => 'Comfortable mid-size vehicles with good balance of space and fuel efficiency.',
                'base_price_per_day' => 40.00,
                'is_active' => true,
            ],
            [
                'name' => 'Full-size Car',
                'description' => 'Spacious and comfortable cars perfect for longer trips and business travel.',
                'base_price_per_day' => 55.00,
                'is_active' => true,
            ],
            [
                'name' => 'Luxury Car',
                'description' => 'Premium vehicles with advanced features and superior comfort for special occasions.',
                'base_price_per_day' => 85.00,
                'is_active' => true,
            ],
            [
                'name' => 'SUV',
                'description' => 'Sport Utility Vehicles with high seating position and cargo space for families.',
                'base_price_per_day' => 65.00,
                'is_active' => true,
            ],
            [
                'name' => 'Van',
                'description' => 'Large capacity vehicles perfect for group travel and cargo transportation.',
                'base_price_per_day' => 75.00,
                'is_active' => true,
            ],
            [
                'name' => 'Truck',
                'description' => 'Heavy-duty vehicles for moving and hauling large items.',
                'base_price_per_day' => 80.00,
                'is_active' => true,
            ],
        ];

        foreach ($vehicleTypes as $typeData) {
            VehicleType::create($typeData);
        }

        // Create vehicles
        $vehicles = [
            // Economy Cars
            [
                'vehicle_type_id' => 1,
                'make' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2023,
                'license_plate' => 'ECO-001',
                'color' => 'White',
                'seats' => 5,
                'features' => ['Air Conditioning', 'Bluetooth', 'USB Ports'],
                'price_per_day' => 25.00,
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'vehicle_type_id' => 1,
                'make' => 'Honda',
                'model' => 'Civic',
                'year' => 2023,
                'license_plate' => 'ECO-002',
                'color' => 'Silver',
                'seats' => 5,
                'features' => ['Air Conditioning', 'Bluetooth', 'Backup Camera'],
                'price_per_day' => 27.00,
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'vehicle_type_id' => 1,
                'make' => 'Nissan',
                'model' => 'Sentra',
                'year' => 2022,
                'license_plate' => 'ECO-003',
                'color' => 'Blue',
                'seats' => 5,
                'features' => ['Air Conditioning', 'Bluetooth'],
                'price_per_day' => 24.00,
                'status' => 'available',
                'is_active' => true,
            ],

            // Compact Cars
            [
                'vehicle_type_id' => 2,
                'make' => 'Honda',
                'model' => 'Fit',
                'year' => 2023,
                'license_plate' => 'COM-001',
                'color' => 'Red',
                'seats' => 5,
                'features' => ['Air Conditioning', 'Bluetooth', 'USB Ports'],
                'price_per_day' => 30.00,
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'vehicle_type_id' => 2,
                'make' => 'Toyota',
                'model' => 'Yaris',
                'year' => 2023,
                'license_plate' => 'COM-002',
                'color' => 'Black',
                'seats' => 5,
                'features' => ['Air Conditioning', 'Bluetooth', 'Backup Camera'],
                'price_per_day' => 32.00,
                'status' => 'available',
                'is_active' => true,
            ],

            // Mid-size Cars
            [
                'vehicle_type_id' => 3,
                'make' => 'Toyota',
                'model' => 'Camry',
                'year' => 2023,
                'license_plate' => 'MID-001',
                'color' => 'Gray',
                'seats' => 5,
                'features' => ['Air Conditioning', 'Bluetooth', 'Navigation', 'Backup Camera'],
                'price_per_day' => 40.00,
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'vehicle_type_id' => 3,
                'make' => 'Honda',
                'model' => 'Accord',
                'year' => 2023,
                'license_plate' => 'MID-002',
                'color' => 'White',
                'seats' => 5,
                'features' => ['Air Conditioning', 'Bluetooth', 'Navigation', 'Sunroof'],
                'price_per_day' => 42.00,
                'status' => 'available',
                'is_active' => true,
            ],

            // Full-size Cars
            [
                'vehicle_type_id' => 4,
                'make' => 'Chevrolet',
                'model' => 'Malibu',
                'year' => 2023,
                'license_plate' => 'FUL-001',
                'color' => 'Black',
                'seats' => 5,
                'features' => ['Air Conditioning', 'Bluetooth', 'Navigation', 'Leather Seats'],
                'price_per_day' => 55.00,
                'status' => 'available',
                'is_active' => true,
            ],

            // Luxury Cars
            [
                'vehicle_type_id' => 5,
                'make' => 'BMW',
                'model' => '3 Series',
                'year' => 2023,
                'license_plate' => 'LUX-001',
                'color' => 'Black',
                'seats' => 5,
                'features' => ['Premium Sound', 'Navigation', 'Leather Seats', 'Sunroof', 'Heated Seats'],
                'price_per_day' => 85.00,
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'vehicle_type_id' => 5,
                'make' => 'Mercedes-Benz',
                'model' => 'C-Class',
                'year' => 2023,
                'license_plate' => 'LUX-002',
                'color' => 'Silver',
                'seats' => 5,
                'features' => ['Premium Sound', 'Navigation', 'Leather Seats', 'Sunroof', 'Climate Control'],
                'price_per_day' => 90.00,
                'status' => 'available',
                'is_active' => true,
            ],

            // SUVs
            [
                'vehicle_type_id' => 6,
                'make' => 'Toyota',
                'model' => 'RAV4',
                'year' => 2023,
                'license_plate' => 'SUV-001',
                'color' => 'Blue',
                'seats' => 5,
                'features' => ['Air Conditioning', 'Bluetooth', 'Navigation', 'All-Wheel Drive'],
                'price_per_day' => 65.00,
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'vehicle_type_id' => 6,
                'make' => 'Honda',
                'model' => 'CR-V',
                'year' => 2023,
                'license_plate' => 'SUV-002',
                'color' => 'White',
                'seats' => 5,
                'features' => ['Air Conditioning', 'Bluetooth', 'Navigation', 'Backup Camera'],
                'price_per_day' => 67.00,
                'status' => 'available',
                'is_active' => true,
            ],

            // Vans
            [
                'vehicle_type_id' => 7,
                'make' => 'Honda',
                'model' => 'Odyssey',
                'year' => 2023,
                'license_plate' => 'VAN-001',
                'color' => 'Gray',
                'seats' => 8,
                'features' => ['Air Conditioning', 'Bluetooth', 'Navigation', 'DVD Player'],
                'price_per_day' => 75.00,
                'status' => 'available',
                'is_active' => true,
            ],

            // Trucks
            [
                'vehicle_type_id' => 8,
                'make' => 'Ford',
                'model' => 'F-150',
                'year' => 2023,
                'license_plate' => 'TRK-001',
                'color' => 'Red',
                'seats' => 5,
                'features' => ['Air Conditioning', 'Bluetooth', '4WD', 'Towing Package'],
                'price_per_day' => 80.00,
                'status' => 'available',
                'is_active' => true,
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            Vehicle::create($vehicleData);
        }

        // Create some sample bookings
        $bookings = [
            [
                'user_id' => 2, // John Doe
                'vehicle_id' => 1, // Toyota Corolla
                'start_date' => now()->addDays(1),
                'end_date' => now()->addDays(4),
                'price_per_day' => 25.00,
                'status' => 'confirmed',
                'pickup_location' => 'Downtown Office',
                'dropoff_location' => 'Downtown Office',
                'notes' => 'Need GPS navigation',
                'confirmed_at' => now(),
            ],
            [
                'user_id' => 3, // Jane Smith
                'vehicle_id' => 6, // Toyota Camry
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(10),
                'price_per_day' => 40.00,
                'status' => 'pending',
                'pickup_location' => 'Airport',
                'dropoff_location' => 'Airport',
                'notes' => 'Business trip',
            ],
            [
                'user_id' => 4, // Mike Johnson
                'vehicle_id' => 11, // Toyota RAV4
                'start_date' => now()->subDays(5),
                'end_date' => now()->subDays(2),
                'price_per_day' => 65.00,
                'status' => 'completed',
                'pickup_location' => 'City Center',
                'dropoff_location' => 'City Center',
                'notes' => 'Family vacation',
                'confirmed_at' => now()->subDays(6),
            ],
        ];

        foreach ($bookings as $bookingData) {
            Booking::create($bookingData);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin credentials: admin@vehiclerent.com / password');
        $this->command->info('Customer credentials: john@example.com / password');
    }
}