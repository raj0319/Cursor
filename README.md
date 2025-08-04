# Vehicle Booking System

A modern, responsive vehicle booking system built with Laravel 11, featuring customer booking management and comprehensive admin panel.

## 🚀 Features

### Customer Features
- **Browse Vehicles**: View available vehicles by type with detailed information
- **Advanced Search**: Filter vehicles by type, price, seats, and availability dates
- **Easy Booking**: Create, view, update, and cancel bookings
- **Booking Management**: Track booking status and history
- **Responsive Design**: Works perfectly on all devices

### Admin Features
- **Dashboard**: Comprehensive overview with statistics and charts
- **Vehicle Management**: Full CRUD operations for vehicles and vehicle types
- **Booking Management**: Manage all customer bookings with status updates
- **User Management**: View and manage customer accounts
- **Reports**: Export booking data and generate reports

### Technical Features
- **Modern UI/UX**: Built with Bootstrap 5 and custom CSS
- **Responsive Design**: Mobile-first approach
- **Real-time Validation**: Client-side and server-side validation
- **AJAX Functionality**: Smooth user experience with AJAX requests
- **Security**: Role-based access control and CSRF protection
- **Database**: MySQL with proper relationships and constraints

## 🛠️ Technology Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Bootstrap 5, Custom CSS, Vanilla JavaScript
- **Database**: MySQL 8.0+
- **Icons**: Font Awesome 6
- **Fonts**: Google Fonts (Inter)

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js & NPM (optional, for asset compilation)
- Web server (Apache/Nginx)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/vehicle-booking-system.git
cd vehicle-booking-system
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
```bash
cp .env.example .env
```

Edit the `.env` file and configure your database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vehicle_booking
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Create Database
Create a MySQL database named `vehicle_booking` (or whatever you specified in `.env`)

### 6. Run Migrations
```bash
php artisan migrate
```

### 7. Seed Database (Optional)
```bash
php artisan db:seed
```

This will create:
- Admin user: `admin@vehiclerent.com` / `password`
- Sample customers: `john@example.com` / `password`
- Sample vehicle types and vehicles
- Sample bookings

### 8. Create Storage Link
```bash
php artisan storage:link
```

### 9. Start Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## 📁 Project Structure

```
vehicle-booking-system/
├── app/
│   ├── Http/Controllers/
│   │   ├── HomeController.php
│   │   ├── Customer/
│   │   │   └── BookingController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── VehicleController.php
│   │       ├── VehicleTypeController.php
│   │       └── BookingController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Vehicle.php
│   │   ├── VehicleType.php
│   │   └── Booking.php
│   └── Http/Middleware/
│       └── AdminMiddleware.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── auth/
│       ├── customer/
│       └── admin/
├── public/
│   ├── css/
│   ├── js/
│   └── images/
└── routes/
    └── web.php
```

## 🎯 Usage

### Customer Workflow
1. **Browse Vehicles**: Visit the homepage to see featured vehicles and types
2. **Search**: Use the search functionality to find vehicles by criteria
3. **Register/Login**: Create an account or login to make bookings
4. **Book Vehicle**: Select dates and create a booking
5. **Manage Bookings**: View, edit, or cancel bookings from the dashboard

### Admin Workflow
1. **Login**: Access admin panel with admin credentials
2. **Dashboard**: View system statistics and recent activities
3. **Manage Vehicle Types**: Add/edit/delete vehicle categories
4. **Manage Vehicles**: Add/edit/delete individual vehicles
5. **Manage Bookings**: View all bookings and update their status
6. **Reports**: Export booking data for analysis

## 🔐 Default Credentials

After running the seeder:

**Admin Account:**
- Email: `admin@vehiclerent.com`
- Password: `password`

**Customer Account:**
- Email: `john@example.com`
- Password: `password`

## 🎨 Customization

### Styling
- Main CSS file: `public/css/app.css`
- Color scheme can be modified in CSS custom properties
- Bootstrap variables can be overridden

### Configuration
- Vehicle types and features can be customized in the seeder
- Booking rules can be modified in the models
- Email templates can be customized in resources/views

## 📱 Responsive Design

The application is fully responsive and optimized for:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (< 768px)

## 🔒 Security Features

- CSRF protection on all forms
- Role-based access control (Admin/Customer)
- Input validation and sanitization
- SQL injection prevention through Eloquent ORM
- XSS protection through Blade templating

## 🚀 Deployment

### Production Setup
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Configure proper database credentials
4. Set up web server (Apache/Nginx)
5. Configure SSL certificate
6. Set up automated backups
7. Configure email settings for notifications

### Web Server Configuration
Ensure your web server points to the `public` directory and has proper rewrite rules for Laravel.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 API Documentation

The system includes API endpoints for AJAX functionality:

- `POST /api/check-availability` - Check vehicle availability
- `GET /api/vehicles/{vehicle}/booking-form` - Get booking form

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error:**
- Verify database credentials in `.env`
- Ensure MySQL service is running
- Check if database exists

**Permission Errors:**
- Set proper permissions: `chmod -R 755 storage bootstrap/cache`
- Ensure web server has write access to storage and cache directories

**Missing Dependencies:**
- Run `composer install` to install PHP dependencies
- Clear cache: `php artisan cache:clear`

## 📊 Database Schema

### Main Tables
- `users` - Customer and admin accounts
- `vehicle_types` - Categories of vehicles (Car, SUV, etc.)
- `vehicles` - Individual vehicle records
- `bookings` - Customer booking records

### Relationships
- User has many Bookings
- Vehicle belongs to VehicleType
- Vehicle has many Bookings
- Booking belongs to User and Vehicle

## 🔄 Updates

To update the application:
1. Pull latest changes: `git pull origin main`
2. Update dependencies: `composer update`
3. Run migrations: `php artisan migrate`
4. Clear cache: `php artisan cache:clear`

## 📞 Support

For support and questions:
- Create an issue on GitHub
- Email: support@vehiclerent.com
- Documentation: Check the wiki section

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Laravel Framework Team
- Bootstrap Team
- Font Awesome
- Google Fonts
- All contributors and testers

---

**Built with ❤️ using Laravel 11**
