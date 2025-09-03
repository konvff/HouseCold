# House Call Scheduler System - Complete Overview

## 🎯 **System Purpose**
A full-stack web application that enables customers to book house call services, technicians to accept/decline appointments, and automatic payment processing based on actual work time.

## 🏗️ **System Architecture**

### **Database Structure**
```
users (with roles: admin, technician, customer)
├── technicians (technician profiles)
│   └── technician_availabilities (weekly schedules)
├── service_types (available services with pricing)
├── appointments (customer bookings)
│   ├── payment_holds (Stripe payment authorizations)
│   └── time_logs (work time tracking)
```

### **User Roles & Permissions**
- **Admin**: Full system access, manage services, technicians, appointments
- **Technician**: Accept/decline appointments, start/stop timers, view work history
- **Customer**: Book appointments, view status, make payments

## 🚀 **Core Features**

### **1. Customer Booking System**
- **Multi-step form**: Service selection → Date/Time → Details → Payment
- **Real-time availability**: Shows available time slots based on technician schedules
- **Payment authorization**: Stripe integration with card hold (not charged until completion)
- **Confirmation flow**: Email/SMS notifications and status updates

### **2. Technician Management**
- **SMS notifications**: Twilio integration for new appointment alerts
- **Accept/Decline system**: First technician to accept gets the job
- **Work timer**: Start/stop functionality for accurate billing
- **Dashboard**: View pending, confirmed, and completed appointments

### **3. Admin Controls**
- **Service management**: Add/edit service types and pricing
- **Technician oversight**: Manage accounts, availability, and assignments
- **Appointment monitoring**: View all bookings and override assignments
- **System analytics**: Dashboard with statistics and trends

### **4. Payment Processing**
- **Authorization hold**: Pre-authorize card for estimated amount
- **Time-based billing**: Calculate final cost based on actual work time
- **Automatic capture**: Process payment when work is completed
- **Refund support**: Handle overcharges and adjustments

## 📱 **User Interfaces**

### **Public Pages**
- **Homepage** (`/`): Landing page with service overview
- **Booking Form** (`/book-appointment`): Multi-step appointment creation
- **Available Slots** (`/available-slots`): API endpoint for time slot data

### **Protected Dashboards**
- **Customer Dashboard** (`/dashboard`): View appointments and history
- **Technician Dashboard** (`/technician/dashboard`): Work management interface
- **Admin Dashboard** (`/admin/dashboard`): System oversight and management

### **Management Interfaces**
- **Service Types** (`/service-types`): CRUD operations for services
- **Technicians** (`/technicians`): Manage technician accounts
- **Appointments** (`/appointments`): View and manage all bookings

## 🔧 **Technical Implementation**

### **Backend (Laravel 11)**
- **Controllers**: Handle HTTP requests and business logic
- **Models**: Eloquent ORM with relationships and business rules
- **Middleware**: Role-based access control and authentication
- **Migrations**: Database schema management
- **Seeders**: Sample data for testing and development

### **Frontend**
- **Bootstrap 5**: Responsive UI framework
- **Vanilla JavaScript**: Interactive functionality (timers, form validation)
- **Stripe Elements**: Secure payment form integration
- **Flatpickr**: Date/time picker for appointment scheduling

### **External Integrations**
- **Stripe**: Payment processing and card management
- **Twilio**: SMS notifications for technicians
- **Laravel Sanctum**: API authentication

## 🔄 **Workflow Examples**

### **Customer Booking Flow**
1. Customer visits `/book-appointment`
2. Selects service type (e.g., HVAC Installation)
3. Chooses preferred date and time slot
4. Enters contact information and address
5. Provides payment card details
6. System creates appointment with "pending" status
7. Available technicians receive SMS notifications
8. Customer waits for technician acceptance

### **Technician Work Flow**
1. Technician receives SMS: "New appointment request at [date/time], [service], [address]. Reply YES to accept."
2. Technician logs into dashboard and sees pending appointments
3. Technician accepts appointment → status changes to "confirmed"
4. Customer receives confirmation with technician details
5. On appointment day, technician arrives and starts timer
6. When work is complete, technician stops timer
7. System calculates final cost and processes payment
8. Customer receives receipt and invoice

### **Payment Flow**
1. **Authorization**: Card pre-authorized for estimated amount (e.g., $200 for 2-hour service)
2. **Work Completion**: Technician timer determines actual work time
3. **Final Calculation**: If actual cost ≤ hold amount, capture actual amount
4. **Overcharge Handling**: If actual cost > hold amount, capture hold amount + flag for admin review

## 🛡️ **Security Features**

### **Authentication & Authorization**
- **Laravel Sanctum**: Secure API authentication
- **Role-based Access Control**: Admin, Technician, Customer permissions
- **Middleware Protection**: Route-level security enforcement

### **Payment Security**
- **Stripe Integration**: PCI-compliant payment processing
- **Card Tokenization**: No sensitive data stored locally
- **Authorization Holds**: Prevents overcharging

### **Data Protection**
- **Input Validation**: Server-side validation for all forms
- **CSRF Protection**: Built-in Laravel security
- **SQL Injection Prevention**: Eloquent ORM with prepared statements

## 📊 **Database Schema Details**

### **Key Tables & Relationships**

#### **users**
- Primary user accounts with role-based access
- Fields: id, name, email, password, role, phone, location, about_me
- Relationships: hasOne(technician), hasMany(appointments)

#### **service_types**
- Available services with pricing information
- Fields: id, name, description, hourly_rate, estimated_duration_minutes, is_active
- Relationships: hasMany(appointments)

#### **technicians**
- Technician profiles and specializations
- Fields: id, user_id, phone, specialties, status, hourly_rate
- Relationships: belongsTo(user), hasMany(availabilities), hasMany(appointments)

#### **technician_availabilities**
- Weekly schedules and availability windows
- Fields: id, technician_id, day_of_week, start_time, end_time, is_recurring
- Relationships: belongsTo(technician)

#### **appointments**
- Customer bookings and service requests
- Fields: id, customer_name, customer_phone, customer_address, service_type_id, technician_id, status, scheduled_at, estimated_cost, actual_cost
- Relationships: belongsTo(serviceType), belongsTo(technician), hasOne(paymentHold), hasMany(timeLogs)

#### **payment_holds**
- Stripe payment authorizations and status
- Fields: id, appointment_id, amount, status, stripe_payment_intent_id, expires_at
- Relationships: belongsTo(appointment)

#### **time_logs**
- Work time tracking and billing calculations
- Fields: id, appointment_id, technician_id, started_at, ended_at, duration_minutes, notes
- Relationships: belongsTo(appointment), belongsTo(technician)

## 🚀 **Getting Started**

### **Prerequisites**
- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Node.js & NPM

### **Installation Steps**
1. **Clone repository** and install dependencies
2. **Configure environment** (.env file with database, Stripe, Twilio credentials)
3. **Run migrations** to create database tables
4. **Seed database** with sample data
5. **Start development server**

### **Sample Users (after seeding)**
- **Admin**: admin@housecall.com / password123
- **Technician**: tech@housecall.com / password123
- **Customer**: customer@housecall.com / password123

## 🔍 **Testing & Development**

### **Available Routes**
- **Public**: `/`, `/book-appointment`, `/available-slots`
- **Customer**: `/dashboard`, `/profile`, `/billing`
- **Technician**: `/technician/dashboard`
- **Admin**: `/admin/dashboard`, `/service-types`, `/technicians`, `/appointments`

### **API Endpoints**
- **Payment**: `/payments/create-intent`, `/payments/capture`
- **Time Tracking**: `/time-logs/start`, `/time-logs/stop`
- **Appointments**: `/appointments/{id}/accept`, `/appointments/{id}/start-timer`

## 📈 **Future Enhancements**

### **Planned Features**
- Mobile app for technicians
- Customer review and rating system
- Advanced scheduling algorithms
- Integration with accounting software
- Multi-language support
- Advanced reporting and analytics
- Real-time notifications and chat
- GPS tracking for technician locations

### **Scalability Considerations**
- Queue system for background jobs
- Redis caching for performance
- Database optimization and indexing
- Load balancing for high traffic
- Microservices architecture

## 🐛 **Troubleshooting**

### **Common Issues**
1. **Route not defined**: Clear route cache with `php artisan route:clear`
2. **Payment failures**: Verify Stripe API keys and webhook configuration
3. **SMS not sending**: Check Twilio credentials and phone number format
4. **Database errors**: Verify database connection and run migrations
5. **Authentication issues**: Check middleware configuration and user roles

### **Debug Mode**
Enable debug mode in `.env`:
```env
APP_DEBUG=true
APP_LOG_LEVEL=debug
```

## 📚 **Documentation & Resources**

### **Key Files**
- **README_HOUSE_CALL_SCHEDULER.md**: Comprehensive setup guide
- **config_example.txt**: Environment configuration template
- **SYSTEM_OVERVIEW.md**: This document

### **Laravel Resources**
- [Laravel Documentation](https://laravel.com/docs)
- [Stripe PHP SDK](https://stripe.com/docs/libraries)
- [Twilio PHP SDK](https://www.twilio.com/docs/libraries/php)

---

**System Status**: ✅ Production Ready
**Last Updated**: September 2024
**Version**: 1.0.0
