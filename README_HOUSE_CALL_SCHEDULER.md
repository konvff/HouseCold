# House Call Scheduler + Payment Hold System

A full-stack Laravel application that allows customers to book house call services, technicians to accept/decline appointments, and automatic payment processing based on actual work time.

## Features

### Customer Features
- **Service Booking**: Select service type, date, and time slot
- **Payment Authorization**: Secure card hold (not charged until completion)
- **Real-time Updates**: Receive notifications when technicians accept
- **Service History**: View past appointments and invoices

### Technician Features
- **SMS Notifications**: Receive alerts for new appointment requests
- **Accept/Decline**: First technician to accept gets the job
- **Time Tracking**: Start/stop timer for accurate billing
- **Work Dashboard**: View pending, confirmed, and completed appointments

### Admin Features
- **Service Management**: Add/edit service types and pricing
- **Technician Management**: Manage technician accounts and availability
- **Appointment Oversight**: Monitor all appointments and override assignments
- **System Analytics**: View appointment statistics and trends

### Payment System
- **Stripe Integration**: Secure payment processing
- **Authorization Hold**: Card pre-authorized for estimated cost
- **Automatic Capture**: Final charge based on actual time worked
- **Refund Support**: Handle overcharges and adjustments

## Technology Stack

- **Backend**: Laravel 11 (PHP 8.1+)
- **Database**: MySQL/PostgreSQL
- **Payment**: Stripe API
- **SMS**: Twilio API
- **Frontend**: Bootstrap 5, Vanilla JavaScript
- **Authentication**: Laravel Sanctum

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (for asset compilation)

### 1. Clone and Setup
```bash
git clone <repository-url>
cd house-call-scheduler
composer install
npm install
```

### 2. Environment Configuration
Copy `.env.example` to `.env` and configure:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=house_call_scheduler
DB_USERNAME=your_username
DB_PASSWORD=your_password

STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret

TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=your_twilio_phone_number
```

### 3. Database Setup
```bash
php artisan migrate
php artisan db:seed --class=ServiceTypeSeeder
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Compile Assets
```bash
npm run dev
```

### 6. Start Development Server
```bash
php artisan serve
```

## Database Schema

### Core Tables
- **users**: User accounts with role-based access
- **service_types**: Available services with pricing
- **technicians**: Technician profiles and specialties
- **technician_availabilities**: Weekly schedules and availability
- **appointments**: Customer bookings and status
- **payment_holds**: Stripe payment authorizations
- **time_logs**: Work time tracking and billing

### Key Relationships
- Users can have one technician profile
- Technicians have multiple availability slots
- Appointments link to service types and technicians
- Payment holds track authorization status
- Time logs calculate final billing amounts

## Usage Guide

### Customer Booking Flow
1. Visit `/book-appointment`
2. Select service type and preferred date
3. Choose available time slot
4. Enter contact and address information
5. Provide payment card details
6. Receive confirmation and wait for technician acceptance

### Technician Workflow
1. Receive SMS notification for new appointment
2. Review appointment details and accept/decline
3. Arrive at customer location
4. Start timer when work begins
5. Stop timer when work completes
6. System automatically processes final payment

### Admin Management
1. Access admin dashboard at `/admin/dashboard`
2. Manage service types and pricing
3. Add/edit technician accounts
4. Monitor appointment statuses
5. Handle payment issues and refunds

## API Endpoints

### Public Endpoints
- `GET /` - Home page
- `GET /book-appointment` - Appointment booking form
- `POST /book-appointment` - Create new appointment
- `GET /available-slots` - Get available time slots

### Protected Endpoints
- `GET /technician/dashboard` - Technician dashboard
- `POST /appointments/{id}/accept` - Accept appointment
- `POST /appointments/{id}/start-timer` - Start work timer
- `POST /appointments/{id}/stop-timer` - Stop work timer

### Admin Endpoints
- `GET /admin/dashboard` - Admin dashboard
- `GET /service-types` - Manage service types
- `GET /technicians` - Manage technicians
- `GET /appointments` - View all appointments

## Payment Flow

### 1. Authorization Hold
- Customer provides card details
- Stripe creates payment intent with manual capture
- Card is authorized for estimated amount
- No charge is made yet

### 2. Service Completion
- Technician starts and stops timer
- System calculates actual work time
- Final cost is determined

### 3. Payment Capture
- If actual cost ≤ hold amount: capture actual amount
- If actual cost > hold amount: capture hold amount + flag for admin
- Customer receives receipt via email

## SMS Integration

### Twilio Setup
1. Create Twilio account and get credentials
2. Configure phone number for outgoing SMS
3. Update environment variables
4. Test SMS delivery

### Message Templates
- **New Appointment**: "New appointment request at [date/time], [service], [address]. Reply YES to accept."
- **Already Accepted**: "Appointment already accepted by another technician."

## Security Features

- **Role-based Access Control**: Admin, Technician, Customer roles
- **Payment Security**: Stripe handles sensitive card data
- **Input Validation**: Server-side validation for all forms
- **CSRF Protection**: Laravel built-in CSRF tokens
- **SQL Injection Prevention**: Eloquent ORM with prepared statements

## Testing

### Run Tests
```bash
php artisan test
```

### Test Scenarios
- Customer booking flow
- Technician acceptance workflow
- Timer functionality
- Payment processing
- Admin management features

## Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Configure production database
- [ ] Set up SSL certificates
- [ ] Configure web server (Apache/Nginx)
- [ ] Set up queue workers for background jobs
- [ ] Configure logging and monitoring
- [ ] Set up backup procedures

### Server Requirements
- PHP 8.1+
- MySQL 8.0+ or PostgreSQL 13+
- Redis (for caching and queues)
- SSL certificate
- 2GB+ RAM recommended

## Troubleshooting

### Common Issues
1. **Payment failures**: Check Stripe API keys and webhook configuration
2. **SMS not sending**: Verify Twilio credentials and phone number
3. **Timer not working**: Check JavaScript console for errors
4. **Database connection**: Verify database credentials and permissions

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
APP_LOG_LEVEL=debug
```

## Contributing

1. Fork the repository
2. Create feature branch
3. Make changes and test thoroughly
4. Submit pull request with description

## License

This project is licensed under the MIT License.

## Support

For support and questions:
- Create an issue in the repository
- Check the documentation
- Review the troubleshooting section

## Roadmap

### Future Features
- Mobile app for technicians
- Customer review system
- Advanced scheduling algorithms
- Integration with accounting software
- Multi-language support
- Advanced reporting and analytics
