<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\TechnicianAvailabilityController;
use App\Http\Controllers\SMSWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/book-appointment', [AppointmentController::class, 'create'])->name('appointments.create');
Route::post('/book-appointment', [AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/available-slots', [AppointmentController::class, 'availableSlots'])->name('appointments.available-slots');
Route::get('/payment-confirmation/{appointment}', [AppointmentController::class, 'paymentConfirmation'])->name('payments.confirmation');

// SMS Webhook routes (no authentication required)
Route::post('/sms/webhook', [SMSWebhookController::class, 'handleIncomingSMS'])->name('sms.webhook');
Route::post('/sms/delivery-status', [SMSWebhookController::class, 'handleDeliveryStatus'])->name('sms.delivery-status');

Route::group(['middleware' => 'auth'], function () {

    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('billing', function () {
        return view('billing');
    })->name('billing');

    Route::get('profile', function () {
        return view('profile');
    })->name('profile');

    Route::get('rtl', function () {
        return view('rtl');
    })->name('rtl');

    Route::get('user-management', function () {
        return view('laravel-examples/user-management');
    })->name('user-management');

    Route::get('tables', function () {
        return view('tables');
    })->name('tables');

    Route::get('virtual-reality', function () {
        return view('virtual-reality');
    })->name('virtual-reality');

    Route::get('static-sign-in', function () {
        return view('static-sign-in');
    })->name('sign-in');

    Route::get('static-sign-up', function () {
        return view('static-sign-up');
    })->name('sign-up');

    Route::post('/logout', [SessionsController::class, 'destroy'])->name('logout');
    Route::get('/user-profile', [InfoUserController::class, 'create']);
    Route::post('/user-profile', [InfoUserController::class, 'store']);
    Route::get('/login', function () {
        return view('dashboard');
    })->name('sign-up');

    // Admin routes
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
        Route::resource('service-types', ServiceTypeController::class);
        Route::resource('technicians', TechnicianController::class);

        // Technician Availability Management (Admin)
        Route::resource('technician-availabilities', TechnicianAvailabilityController::class)->parameters([
            'technician-availabilities' => 'availability'
        ]);



        Route::resource('appointments', AppointmentController::class)->except(['create', 'store']);
        Route::post('/appointments/{appointment}/assign-technician', [AppointmentController::class, 'assignTechnician'])->name('appointments.assign-technician');
        Route::post('/appointments/{appointment}/capture-payment', [AppointmentController::class, 'capturePayment'])->name('appointments.capture-payment');
        Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancelAppointment'])->name('appointments.cancel');
    });

            // Technician routes
    Route::middleware(['auth', 'role:technician'])->group(function () {
        Route::get('/technician/dashboard', [TechnicianController::class, 'dashboard'])->name('technician.dashboard');

        // Technician's own availability management
        Route::get('my-availability', [TechnicianAvailabilityController::class, 'myAvailability'])->name('technician-availabilities.my-availability');
        Route::post('my-availability', [TechnicianAvailabilityController::class, 'storeMyAvailability'])->name('technician-availabilities.store-my');
        Route::put('my-availability/{technician_availability}', [TechnicianAvailabilityController::class, 'updateMyAvailability'])->name('technician-availabilities.update-my');
        Route::delete('my-availability/{technician_availability}', [TechnicianAvailabilityController::class, 'destroyMyAvailability'])->name('technician-availabilities.destroy-my');

        Route::post('/appointments/{appointment}/accept', [TechnicianController::class, 'acceptAppointment'])->name('appointments.accept');
        Route::post('/appointments/{appointment}/decline', [TechnicianController::class, 'declineAppointment'])->name('appointments.decline');
        Route::post('/appointments/{appointment}/start-timer', [TechnicianController::class, 'startTimer'])->name('appointments.start-timer');
        Route::post('/appointments/{appointment}/stop-timer', [TechnicianController::class, 'stopTimer'])->name('appointments.stop-timer');
        Route::post('/appointments/{appointment}/pause-timer', [TechnicianController::class, 'pauseTimer'])->name('appointments.pause-timer');
    });

    // Time log routes (for technicians)
    Route::middleware(['auth', 'role:technician'])->group(function () {
        Route::post('/time-logs/start', [TimeLogController::class, 'startTimer'])->name('time-logs.start');
        Route::post('/time-logs/stop', [TimeLogController::class, 'stopTimer'])->name('time-logs.stop');
        Route::post('/time-logs/pause', [TimeLogController::class, 'pauseTimer'])->name('time-logs.pause');
        Route::post('/time-logs/resume', [TimeLogController::class, 'resumeTimer'])->name('time-logs.resume');
        Route::get('/time-logs/current', [TimeLogController::class, 'getCurrentTimer'])->name('time-logs.current');
        Route::put('/time-logs/{timeLog}/notes', [TimeLogController::class, 'updateNotes'])->name('time-logs.update-notes');
    });

    // Payment routes
    Route::post('/payments/create-intent', [PaymentController::class, 'createPaymentIntent'])->name('payments.create-intent');
    Route::post('/payments/confirm', [PaymentController::class, 'confirmPayment'])->name('payments.confirm');
    Route::post('/payments/capture', [PaymentController::class, 'capturePayment'])->name('payments.capture');
    Route::post('/payments/refund', [PaymentController::class, 'refundPayment'])->name('payments.refund');
    Route::get('/payments/status', [PaymentController::class, 'getPaymentStatus'])->name('payments.status');
});


    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/session', [SessionsController::class, 'store'])->name('session.store');
    Route::get('/login/forgot-password', [ResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ResetController::class, 'sendEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');



