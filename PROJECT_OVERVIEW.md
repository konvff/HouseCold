# House Call Scheduler - Professional Laravel Boilerplate

## 🏗️ Architecture Overview

This project implements a comprehensive house call scheduling system with SMS notifications, payment processing, and technician management using professional Laravel patterns.

### 📁 Project Structure

```
app/
├── Enums/                    # Type-safe constants
│   ├── AppointmentStatus.php
│   ├── PaymentStatus.php
│   ├── SMSNotificationStatus.php
│   ├── SMSNotificationType.php
│   ├── TechnicianStatus.php
│   ├── TimeLogStatus.php
│   └── UserRole.php
├── Http/Controllers/         # API & Web controllers
│   ├── AppointmentController.php
│   ├── PaymentController.php
│   ├── SMSWebhookController.php
│   ├── TechnicianController.php
│   └── TimeLogController.php
├── Models/                   # Eloquent models
│   ├── Appointment.php
│   ├── PaymentHold.php
│   ├── SMSNotification.php
│   ├── Technician.php
│   ├── TimeLog.php
│   └── User.php
├── Repositories/             # Data access layer
│   ├── Contracts/            # Repository interfaces
│   └── [Repository implementations]
├── Services/                 # Business logic layer
│   ├── Contracts/            # Service interfaces
│   └── [Service implementations]
└── Providers/                # Service providers
    └── RepositoryServiceProvider.php
```

## 🎯 Core Features

### 1. **SMS-Based Appointment System**
- Customer books appointment → SMS sent to all available technicians
- First technician to reply "YES" gets the appointment
- Automatic assignment and confirmation
- Real-time status updates

### 2. **Payment Processing (Cardknox)**
- Payment authorization on booking
- Automatic capture on job completion
- Refund and void capabilities
- Secure payment hold system

### 3. **Technician Management**
- Availability scheduling
- Service type specialization
- Real-time status tracking
- Performance monitoring

### 4. **Time Tracking**
- Start/pause/stop timer functionality
- Automatic cost calculation
- Work time logging
- Progress tracking

## 🔧 Technical Implementation

### **Enums** - Type Safety
```php
enum AppointmentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
```

### **Repository Pattern** - Data Access
```php
interface AppointmentRepositoryInterface
{
    public function find(int $id): ?Appointment;
    public function create(array $data): Appointment;
    public function findByStatus(string $status): Collection;
}
```

### **Service Layer** - Business Logic
```php
interface AppointmentServiceInterface
{
    public function createAppointment(array $data): Appointment;
    public function assignTechnician(Appointment $appointment, Technician $technician): bool;
    public function processTechnicianResponse(Technician $technician, string $response): void;
}
```

### **Dependency Injection** - Clean Architecture
```php
public function __construct(
    private AppointmentServiceInterface $appointmentService,
    private SMSServiceInterface $smsService
) {}
```

## 📱 SMS Workflow

1. **Customer books appointment** via web form
2. **Payment authorized** with Cardknox
3. **SMS notifications sent** to all available technicians
4. **Technician replies "YES"** to accept
5. **Appointment automatically assigned** to first responder
6. **Other technicians notified** slot was taken
7. **Confirmation SMS sent** to assigned technician

## 💳 Payment Flow

1. **Authorization** - Hold funds when booking
2. **Timer tracking** - Record actual work time
3. **Cost calculation** - Based on hourly rate × time worked
4. **Automatic capture** - Charge actual amount (capped at authorized)
5. **Refund capability** - For cancellations or overcharges

## 🎨 Key Benefits

- **Type Safety** - Enums prevent invalid values
- **Testability** - Easy to mock interfaces
- **Maintainability** - Clear separation of concerns
- **Scalability** - Easy to extend and modify
- **Professional** - Follows Laravel best practices
- **Clean Code** - No AI-looking comments or complex formatting

## 🚀 Usage Examples

### Creating an Appointment
```php
$appointment = $appointmentService->createAppointment([
    'customer_name' => 'John Doe',
    'service_type_id' => 1,
    'scheduled_at' => now()->addHours(2),
    'customer_address' => '123 Main St'
]);
```

### Processing SMS Response
```php
$appointmentService->processTechnicianResponse($technician, 'YES');
```

### Starting Timer
```php
$timeLog = $timeLogService->startTimer($appointment);
```

## 📊 Database Schema

- **users** - User accounts with roles
- **technicians** - Technician profiles and status
- **appointments** - Booking information
- **payment_holds** - Payment authorization data
- **time_logs** - Work time tracking
- **sms_notifications** - SMS communication log
- **technician_availabilities** - Schedule management

## 🔐 Security Features

- Role-based access control
- Payment tokenization
- SMS webhook validation
- CSRF protection
- Input validation and sanitization

This boilerplate provides a solid foundation for any appointment-based service business with professional code structure and modern Laravel patterns.

