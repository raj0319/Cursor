@extends('layouts.app')

@section('title', 'Welcome - Vehicle Booking System')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="display-4 fw-bold mb-4">
                        Find Your Perfect
                        <span class="text-warning">Vehicle Rental</span>
                    </h1>
                    <p class="lead mb-4">
                        Discover a wide range of quality vehicles for rent. From economy cars to luxury SUVs, 
                        we have the perfect vehicle for your needs at competitive prices.
                    </p>
                    
                    <!-- Quick Search Form -->
                    <div class="card shadow-lg">
                        <div class="card-body">
                            <form action="{{ route('search') }}" method="GET" class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-dark">Pick-up Date</label>
                                    <input type="date" name="start_date" class="form-control" 
                                           value="{{ request('start_date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark">Return Date</label>
                                    <input type="date" name="end_date" class="form-control" 
                                           value="{{ request('end_date', date('Y-m-d', strtotime('+3 days'))) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark">Vehicle Type</label>
                                    <select name="vehicle_type_id" class="form-select">
                                        <option value="">All Types</option>
                                        @foreach($vehicleTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark">Seats</label>
                                    <select name="seats" class="form-select">
                                        <option value="">Any</option>
                                        <option value="2">2+ Seats</option>
                                        <option value="4">4+ Seats</option>
                                        <option value="5">5+ Seats</option>
                                        <option value="7">7+ Seats</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-search me-2"></i>
                                        Search Vehicles
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="hero-image text-center">
                    <img src="{{ asset('images/hero-car.png') }}" alt="Vehicle Rental" 
                         class="img-fluid" style="max-height: 400px;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon text-primary mb-3">
                        <i class="fas fa-car fa-3x"></i>
                    </div>
                    <h3 class="stat-number fw-bold">{{ $stats['total_vehicles'] }}+</h3>
                    <p class="stat-label text-muted">Vehicles Available</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon text-success mb-3">
                        <i class="fas fa-calendar-check fa-3x"></i>
                    </div>
                    <h3 class="stat-number fw-bold">{{ $stats['total_bookings'] }}+</h3>
                    <p class="stat-label text-muted">Successful Bookings</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon text-warning mb-3">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                    <h3 class="stat-number fw-bold">{{ $stats['happy_customers'] }}+</h3>
                    <p class="stat-label text-muted">Happy Customers</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <div class="stat-icon text-info mb-3">
                        <i class="fas fa-list fa-3x"></i>
                    </div>
                    <h3 class="stat-number fw-bold">{{ $stats['vehicle_types'] }}+</h3>
                    <p class="stat-label text-muted">Vehicle Categories</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vehicle Types Section -->
<section class="vehicle-types-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title fw-bold">Choose Your Vehicle Type</h2>
            <p class="section-subtitle text-muted">
                We offer a diverse fleet of vehicles to meet all your transportation needs
            </p>
        </div>
        
        <div class="row">
            @foreach($vehicleTypes as $type)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="vehicle-type-card card h-100 shadow-sm border-0">
                        <div class="card-img-top position-relative">
                            <img src="{{ $type->image_url }}" alt="{{ $type->name }}" 
                                 class="img-fluid" style="height: 200px; object-fit: cover; width: 100%;">
                            <div class="vehicle-count-badge position-absolute top-0 end-0 m-3">
                                <span class="badge bg-primary">{{ $type->available_vehicles_count }} Available</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ $type->name }}</h5>
                            <p class="card-text text-muted flex-grow-1">{{ $type->description }}</p>
                            <div class="pricing-info mb-3">
                                <span class="price-label text-muted">Starting from</span>
                                <h4 class="price text-primary fw-bold mb-0">{{ $type->formatted_price }}/day</h4>
                            </div>
                            <a href="{{ route('vehicles.by-type', $type) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>
                                View Vehicles
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('vehicle-types') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-list me-2"></i>
                View All Vehicle Types
            </a>
        </div>
    </div>
</section>

<!-- Featured Vehicles Section -->
<section class="featured-vehicles-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title fw-bold">Featured Vehicles</h2>
            <p class="section-subtitle text-muted">
                Discover our most popular and highly-rated vehicles
            </p>
        </div>
        
        <div class="row">
            @foreach($featuredVehicles as $vehicle)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="vehicle-card card h-100 shadow-sm border-0">
                        <div class="card-img-top position-relative">
                            <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->full_name }}" 
                                 class="img-fluid" style="height: 200px; object-fit: cover; width: 100%;">
                            <div class="vehicle-status-badge position-absolute top-0 start-0 m-3">
                                <span class="badge bg-success">Available</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="vehicle-type-label">
                                <small class="text-muted">{{ $vehicle->vehicleType->name }}</small>
                            </div>
                            <h6 class="card-title fw-bold">{{ $vehicle->full_name }}</h6>
                            
                            <div class="vehicle-features mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i> {{ $vehicle->seats }} Seats
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-palette me-1"></i> {{ $vehicle->color }}
                                </small>
                            </div>
                            
                            <div class="pricing-info mb-3 mt-auto">
                                <h5 class="price text-primary fw-bold mb-0">{{ $vehicle->formatted_price }}/day</h5>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('vehicle.details', $vehicle) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-info-circle me-1"></i>
                                    View Details
                                </a>
                                @auth
                                    <a href="{{ route('customer.bookings.create', $vehicle) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-calendar-plus me-1"></i>
                                        Book Now
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-sign-in-alt me-1"></i>
                                        Login to Book
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('search') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-search me-2"></i>
                Browse All Vehicles
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title fw-bold">Why Choose Us</h2>
            <p class="section-subtitle text-muted">
                We provide the best vehicle rental experience with these amazing features
            </p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon text-primary mb-3">
                        <i class="fas fa-shield-alt fa-3x"></i>
                    </div>
                    <h5 class="feature-title fw-bold">Safe & Secure</h5>
                    <p class="feature-description text-muted">
                        All our vehicles are regularly maintained and inspected for your safety and peace of mind.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon text-success mb-3">
                        <i class="fas fa-dollar-sign fa-3x"></i>
                    </div>
                    <h5 class="feature-title fw-bold">Best Prices</h5>
                    <p class="feature-description text-muted">
                        We offer competitive pricing with no hidden fees. Get the best value for your money.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon text-info mb-3">
                        <i class="fas fa-clock fa-3x"></i>
                    </div>
                    <h5 class="feature-title fw-bold">24/7 Support</h5>
                    <p class="feature-description text-muted">
                        Our customer support team is available round the clock to assist you with any queries.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon text-warning mb-3">
                        <i class="fas fa-mobile-alt fa-3x"></i>
                    </div>
                    <h5 class="feature-title fw-bold">Easy Booking</h5>
                    <p class="feature-description text-muted">
                        Book your vehicle in just a few clicks with our user-friendly online booking system.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon text-danger mb-3">
                        <i class="fas fa-map-marker-alt fa-3x"></i>
                    </div>
                    <h5 class="feature-title fw-bold">Multiple Locations</h5>
                    <p class="feature-description text-muted">
                        Pick up and drop off your vehicle at any of our convenient locations across the city.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon text-secondary mb-3">
                        <i class="fas fa-star fa-3x"></i>
                    </div>
                    <h5 class="feature-title fw-bold">Quality Vehicles</h5>
                    <p class="feature-description text-muted">
                        Choose from our fleet of well-maintained, clean, and reliable vehicles from top brands.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Ready to Start Your Journey?</h2>
        <p class="lead mb-4">
            Join thousands of satisfied customers who trust us for their vehicle rental needs.
        </p>
        <div class="cta-buttons">
            @auth
                <a href="{{ route('search') }}" class="btn btn-light btn-lg me-3">
                    <i class="fas fa-search me-2"></i>
                    Browse Vehicles
                </a>
                <a href="{{ route('customer.bookings.index') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-calendar-check me-2"></i>
                    My Bookings
                </a>
            @else
                <a href="{{ route('register') }}" class="btn btn-light btn-lg me-3">
                    <i class="fas fa-user-plus me-2"></i>
                    Sign Up Now
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login
                </a>
            @endauth
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }
    
    .vehicle-type-card,
    .vehicle-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .vehicle-type-card:hover,
    .vehicle-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    
    .stat-card {
        padding: 2rem 1rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
        color: #333;
    }
    
    .feature-card {
        padding: 2rem 1rem;
    }
    
    .main-content {
        padding-top: 76px; /* Account for fixed navbar */
    }
    
    .section-title {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .section-subtitle {
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }
</style>
@endpush
@endsection