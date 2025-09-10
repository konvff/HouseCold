<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repositories
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\AppointmentRepository;
use App\Repositories\Contracts\SMSNotificationRepositoryInterface;
use App\Repositories\SMSNotificationRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\Contracts\TechnicianRepositoryInterface;
use App\Repositories\TechnicianRepository;
use App\Repositories\Contracts\ServiceTypeRepositoryInterface;
use App\Repositories\ServiceTypeRepository;
use App\Repositories\Contracts\PaymentHoldRepositoryInterface;
use App\Repositories\PaymentHoldRepository;
use App\Repositories\Contracts\TechnicianAvailabilityRepositoryInterface;
use App\Repositories\TechnicianAvailabilityRepository;
use App\Repositories\Contracts\TimeLogRepositoryInterface;
use App\Repositories\TimeLogRepository;

// Services
use App\Services\Contracts\SMSServiceInterface;
use App\Services\SMSService;
use App\Services\Contracts\AppointmentServiceInterface;
use App\Services\AppointmentService;
use App\Services\Contracts\UserServiceInterface;
use App\Services\UserService;
use App\Services\Contracts\TechnicianServiceInterface;
use App\Services\TechnicianService;
use App\Services\Contracts\PaymentServiceInterface;
use App\Services\PaymentService;
use App\Services\Contracts\TimeLogServiceInterface;
use App\Services\TimeLogService;
use App\Services\Contracts\TechnicianAvailabilityServiceInterface;
use App\Services\TechnicianAvailabilityService;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository Bindings
        $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
        $this->app->bind(SMSNotificationRepositoryInterface::class, SMSNotificationRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TechnicianRepositoryInterface::class, TechnicianRepository::class);
        $this->app->bind(ServiceTypeRepositoryInterface::class, ServiceTypeRepository::class);
        $this->app->bind(PaymentHoldRepositoryInterface::class, PaymentHoldRepository::class);
        $this->app->bind(TechnicianAvailabilityRepositoryInterface::class, TechnicianAvailabilityRepository::class);
        $this->app->bind(TimeLogRepositoryInterface::class, TimeLogRepository::class);

        // Service Bindings
        $this->app->bind(SMSServiceInterface::class, SMSService::class);
        $this->app->bind(AppointmentServiceInterface::class, AppointmentService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(TechnicianServiceInterface::class, TechnicianService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(TimeLogServiceInterface::class, TimeLogService::class);
        $this->app->bind(TechnicianAvailabilityServiceInterface::class, TechnicianAvailabilityService::class);
    }

    public function boot(): void
    {
        //
    }
}
