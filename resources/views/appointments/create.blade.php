@extends('layouts.guest')

@section('title', 'Book Appointment')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
    :root {
        --primary-color: #3b82f6;
        --secondary-color: #10b981;

        --success-color: #059669;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #06b6d4;
        --light-bg: rgba(255, 255, 255, 0.95);
        --glass-bg: rgba(255, 255, 255, 0.1);
        --glass-border: rgba(255, 255, 255, 0.2);
        --shadow-light: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-medium: 0 10px 25px rgba(0, 0, 0, 0.15);
        --shadow-heavy: 0 20px 40px rgba(0, 0, 0, 0.2);
        --border-radius: 12px;
        --border-radius-lg: 20px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-slow: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .appointment-container {
        max-width: 900px;
            margin: 0 auto;
        padding: 2rem 1rem;
        position: relative;
    }

    .appointment-header {
        text-align: center;
        margin-bottom: 3rem;
        animation: slideInDown 0.8s ease-out;
    }

    .appointment-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
        position: relative;
    }

    .appointment-header h1::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
        animation: expandLine 1s ease-out 0.5s both;
    }

    .appointment-header .lead {
        font-size: 1.2rem;
        color: rgba(255, 255, 255, 0.95);
        font-weight: 500;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        animation: fadeInUp 0.8s ease-out 0.3s both;
    }

    /* Enhanced Step Indicator */
        .step-indicator {
            display: flex;
            justify-content: space-between;
        margin: 3rem 0;
        position: relative;
        padding: 0 2rem;
    }

    .step-indicator::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 2rem;
        right: 2rem;
        height: 3px;
        background: linear-gradient(90deg, var(--glass-bg), var(--glass-border));
        border-radius: 2px;
        z-index: 1;
        animation: progressLine 1.5s ease-out 1s both;
    }

    .step-indicator::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 2rem;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
        z-index: 2;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .step {
        background: var(--light-bg);
        backdrop-filter: blur(20px);
        padding: 1rem 1.5rem;
        border-radius: var(--border-radius-lg);
        border: 2px solid var(--glass-border);
        position: relative;
        z-index: 3;
        font-weight: 600;
        color: #1f2937;
        transition: var(--transition-slow);
        cursor: pointer;
        min-width: 140px;
        text-align: center;
        box-shadow: var(--shadow-light);
        animation: stepAppear 0.6s ease-out both;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .step:nth-child(1) { animation-delay: 0.1s; }
    .step:nth-child(2) { animation-delay: 0.2s; }
    .step:nth-child(3) { animation-delay: 0.3s; }
    .step:nth-child(4) { animation-delay: 0.4s; }

    .step::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: var(--border-radius-lg);
        z-index: -1;
        opacity: 0;
        transition: var(--transition);
    }

    .step:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-medium);
    }

        .step.active {
        color: white;
        transform: translateY(-5px) scale(1.05);
        box-shadow: var(--shadow-heavy);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .step.active::before {
        opacity: 1;
    }

        .step.completed {
        color: white;
        background: linear-gradient(135deg, var(--success-color), #059669);
        border-color: var(--success-color);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .step.completed::after {
        content: '✓';
        position: absolute;
        top: -8px;
        right: -8px;
        width: 24px;
        height: 24px;
        background: var(--success-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        animation: checkmark 0.5s ease-out;
    }

    /* Form Sections */
        .form-section {
            display: none;
        animation: sectionSlideIn 0.6s ease-out;
        opacity: 0;
        transform: translateX(30px);
        }

        .form-section.active {
            display: block;
        opacity: 1;
        transform: translateX(0);
    }

    .form-section.slide-out {
        animation: sectionSlideOut 0.4s ease-in;
    }

    /* Enhanced Cards */
    .form-card {
        background: var(--light-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-medium);
        overflow: hidden;
        transition: var(--transition);
        position: relative;
    }

    .form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }

    .form-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-heavy);
    }

    .card-header {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid var(--glass-border);
        padding: 1.5rem;
    }

    .card-header h5 {
        color: var(--primary-color);
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 2rem;
    }

    /* Time Slots */
    .time-slots-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .time-slot {
        border: 2px solid var(--glass-border);
        border-radius: var(--border-radius);
        padding: 1.75rem 1.25rem;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
        background: var(--light-bg);
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
        min-height: 130px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .time-slot .time-display {
        font-size: 1.2rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 0.75rem;
        line-height: 1.3;
        text-shadow: none;
        background: none;
        -webkit-background-clip: unset;
        -webkit-text-fill-color: unset;
    }


    .time-slot .time-display i {
        color: var(--primary-color);
    }

    /* Enhanced selection state */
    .time-slot.selected .time-display {
        color: #1f2937;
        font-weight: 900;
    }

    .time-slot.selected .time-display i {
        color: var(--primary-color);
    }

    .time-slot::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: left 0.5s;
    }

        .time-slot:hover {
        border-color: var(--primary-color);
        transform: translateY(-3px);
        box-shadow: var(--shadow-light);
        }

    .time-slot:hover::before {
        left: 100%;
    }

        .time-slot.selected {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        transform: translateY(-3px) scale(1.02);
        box-shadow: var(--shadow-medium);
    }

    .time-slot.selected::after {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 8px;
        width: 20px;
        height: 20px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: bold;
        animation: checkmark 0.3s ease-out;
    }

    /* Enhanced Buttons */
    .btn-nav {
        padding: 0.875rem 2.5rem;
        border-radius: var(--border-radius);
        font-weight: 600;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        border: none;
    }

    .btn-nav::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-nav:hover::before {
        left: 100%;
    }

    .btn-primary-nav {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary-nav:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-secondary-nav {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .btn-secondary-nav:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
        color: white;
    }

    /* Payment Form */
        .payment-form {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--border-radius-lg);
        padding: 2rem;
        margin-top: 2rem;
        position: relative;
        overflow: hidden;
    }

    .payment-form::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--success-color), #059669);
    }

    /* Animations */
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes expandLine {
        from {
            width: 0;
        }
        to {
            width: 80px;
        }
    }

    @keyframes progressLine {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes stepAppear {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.8);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes sectionSlideIn {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes sectionSlideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(-30px);
        }
    }

    @keyframes checkmark {
        0% {
            transform: scale(0);
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .appointment-container {
            padding: 1rem 0.5rem;
        }

        .appointment-header h1 {
            font-size: 2rem;
        }

        .step-indicator {
            flex-direction: column;
            gap: 1rem;
            padding: 0;
        }

        .step-indicator::before,
        .step-indicator::after {
            display: none;
        }

        .step {
            min-width: auto;
            width: 100%;
        }

        .time-slots-container {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 0.75rem;
        }

        .time-slot {
            min-height: 110px;
            padding: 1.25rem 0.75rem;
        }

        .time-slot .time-display {
            font-size: 1.1rem;
            font-weight: 800;
        }


        .btn-nav {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }

    @media (max-width: 576px) {
        .appointment-header h1 {
            font-size: 1.75rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .time-slots-container {
            grid-template-columns: 1fr;
        }
        }
    </style>
@endpush

@section('content')
<div class="appointment-container">
    <!-- Header Section -->
    <div class="appointment-header">
        <h1><i class="fas fa-calendar-plus me-3"></i>Book Your Appointment</h1>
                <p class="lead">Schedule a professional service at your convenience</p>
            </div>

    <!-- Enhanced Step Indicator -->
            <div class="step-indicator">
        <div class="step active" id="step-1" data-step="1">
            <i class="fas fa-tools me-2"></i>Service & Time
        </div>
        <div class="step" id="step-2" data-step="2">
            <i class="fas fa-user me-2"></i>Details
        </div>
        <div class="step" id="step-3" data-step="3">
            <i class="fas fa-credit-card me-2"></i>Payment
        </div>
        <div class="step" id="step-4" data-step="4">
            <i class="fas fa-check-circle me-2"></i>Confirmation
        </div>
            </div>

            <form id="appointment-form" method="POST" action="{{ route('appointments.store') }}">
                @csrf

                <!-- Step 1: Service & Time Selection -->
                <div class="form-section active" id="section-1">
                    <div class="card form-card">
                        <div class="card-header">
                            <h5><i class="fas fa-tools me-2"></i>Select Service & Time</h5>
                            @if($isGuest)
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                You're booking as a guest. You can create an account during checkout for easier future bookings.
                            </small>
                            @else
                            <small class="text-success">
                                <i class="fas fa-user-check me-1"></i>
                                Welcome back, {{ $user?->name ?? 'User' }}! Your profile information will be pre-filled.
                            </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="service_type_id" class="form-label">Service Type *</label>
                                <select class="form-select" id="service_type_id" name="service_type_id" required>
                                    <option value="">Choose a service...</option>
                                    @foreach($serviceTypes as $service)
                                        <option value="{{ $service->id }}" data-rate="{{ $service->hourly_rate }}" data-duration="{{ $service->estimated_duration_minutes }}">
                                            {{ $service->name }} - ${{ $service->hourly_rate }}/hour ({{ $service->estimated_duration_minutes }} min)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="appointment_date" class="form-label">Preferred Date *</label>
                                <input type="text" class="form-control" id="appointment_date" name="appointment_date" required readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Available Time Slots</label>
                                <div id="time-slots" class="time-slots-container">
                                    <div class="col-12 text-center">
                                        <p class="text-muted">Select a service and date to see available time slots</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-nav btn-secondary-nav" onclick="window.location.href='{{ route('home') }}'">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>
                                <button type="button" class="btn btn-nav btn-primary-nav" onclick="nextStep()" id="next-btn-1" disabled>
                                    Next<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Customer Details -->
                <div class="form-section" id="section-2">
                    <div class="card form-card">
                        <div class="card-header">
                            <h5><i class="fas fa-user me-2"></i>Your Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name"
                                           value="{{ $user?->name ?? '' }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="customer_phone" name="customer_phone"
                                           value="{{ $user?->phone ?? '' }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="customer_address" class="form-label">Service Address *</label>
                                <input type="text" class="form-control" id="customer_address" name="customer_address"
                                       placeholder="Start typing your address..." required
                                       value="{{ $user?->location ?? '' }}">
                                <small class="form-text text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Type your address and select from the suggestions, or click on the map to set your location.
                                </small>
                                <div id="map" style="height: 300px; width: 100%; margin-top: 10px; border-radius: 8px; border: 1px solid #e9ecef;"></div>
                                <input type="hidden" id="address_lat" name="address_lat">
                                <input type="hidden" id="address_lng" name="address_lng">
                                <input type="hidden" id="address_components" name="address_components">
                            </div>
                            @if($isGuest)
                            <div class="mb-3">
                                <label for="customer_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="customer_email" name="customer_email"
                                       placeholder="Optional - for appointment updates">
                            </div>
                            @endif
                            <div class="mb-3">
                                <label for="service_notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="service_notes" name="service_notes" rows="3" placeholder="Any specific requirements or details about your service..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-nav btn-secondary-nav" onclick="prevStep()">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-nav btn-primary-nav" onclick="nextStep()">
                                    Next<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Payment -->
                <div class="form-section" id="section-3">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6>Service Summary</h6>
                                    <p><strong>Service:</strong> <span id="summary-service">-</span></p>
                                    <p><strong>Date & Time:</strong> <span id="summary-datetime">-</span></p>
                                    <p><strong>Estimated Cost:</strong> <span id="summary-cost">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Payment Method</h6>
                                    <p class="text-muted">Your card will be authorized but not charged until the service is completed.</p>
                                </div>
                            </div>

                            <div class="payment-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="card_number" class="form-label">Card Number *</label>
                                        <input type="text" class="form-control" id="card_number" name="card_data[card_number]" required maxlength="19" placeholder="1234 5678 9012 3456">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="card_type" class="form-label">Card Type</label>
                                        <input type="text" class="form-control" id="card_type" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="expiry_month" class="form-label">Expiry Month *</label>
                                        <select class="form-select" id="expiry_month" name="card_data[expiry_month]" required>
                                            <option value="">MM</option>
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="expiry_year" class="form-label">Expiry Year *</label>
                                        <select class="form-select" id="expiry_year" name="card_data[expiry_year]" required>
                                            <option value="">YYYY</option>
                                            @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="cvv" class="form-label">CVV *</label>
                                        <input type="text" class="form-control" id="cvv" name="card_data[cvv]" required maxlength="4" placeholder="123">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" class="form-control" id="first_name" name="customer_info[first_name]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" id="last_name" name="customer_info[last_name]" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="customer_info[email]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone *</label>
                                        <input type="tel" class="form-control" id="phone" name="customer_info[phone]" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Billing Address *</label>
                                    <input type="text" class="form-control" id="address" name="customer_info[address]" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="city" class="form-label">City *</label>
                                        <input type="text" class="form-control" id="city" name="customer_info[city]" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="state" class="form-label">State *</label>
                                        <input type="text" class="form-control" id="state" name="customer_info[state]" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="zip" class="form-label">ZIP Code *</label>
                                        <input type="text" class="form-control" id="zip" name="customer_info[zip]" required>
                                    </div>
                                </div>
                                <div id="card-errors" class="text-danger mb-3" role="alert"></div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="prevStep()">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="processPayment()" id="pay-btn">
                                    <i class="fas fa-lock me-2"></i>Authorize Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Confirmation -->
                <div class="form-section" id="section-4">
                    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white;">
                        <div class="card-header" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: none;">
                            <h5 class="mb-0" style="color: white;">
                                <i class="fas fa-check-circle me-2" style="color: #4ade80;"></i>🎉 Payment Confirmed!
                            </h5>
                        </div>
                        <div class="card-body text-center" style="padding: 3rem 2rem;">
                            <div class="success-animation mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 5rem; animation: bounceIn 0.8s ease-out;"></i>
                            </div>

                            <h2 class="mb-3" style="color: white; font-weight: 700;">Congratulations! 🎊</h2>
                            <p class="lead mb-4" style="color: rgba(255,255,255,0.9); font-size: 1.2rem;">
                                Your appointment has been scheduled successfully and payment is authorized!
                            </p>

                            <div class="celebration-icons mb-4" style="display: flex; justify-content: center; gap: 1rem; opacity: 0.8;">
                                <i class="fas fa-star" style="font-size: 1.5rem; animation: float 3s ease-in-out infinite;"></i>
                                <i class="fas fa-heart" style="font-size: 1.5rem; animation: float 3s ease-in-out infinite; animation-delay: 0.5s;"></i>
                                <i class="fas fa-thumbs-up" style="font-size: 1.5rem; animation: float 3s ease-in-out infinite; animation-delay: 1s;"></i>
                                <i class="fas fa-gem" style="font-size: 1.5rem; animation: float 3s ease-in-out infinite; animation-delay: 1.5s;"></i>
                            </div>

                            <div class="confirmation-details mb-4" style="background: rgba(255,255,255,0.1); border-radius: 15px; padding: 1.5rem; backdrop-filter: blur(10px);">
                                <h6 class="mb-3" style="color: #4ade80;">
                                    <i class="fas fa-shield-alt me-2"></i>Payment Status: Secure & Authorized
                                </h6>
                                <p class="mb-2" style="color: rgba(255,255,255,0.9);">
                                    <i class="fas fa-lock me-2"></i>Your card details are encrypted and secure
                                </p>
                                <p class="mb-0" style="color: rgba(255,255,255,0.9);">
                                    <i class="fas fa-credit-card me-2"></i>You'll only be charged after service completion
                                </p>
                            </div>

                            <div class="next-steps mb-4" style="background: rgba(255,255,255,0.1); border-radius: 15px; padding: 1.5rem; backdrop-filter: blur(10px);">
                                <h6 class="mb-3" style="color: #fbbf24;">
                                    <i class="fas fa-list-check me-2"></i>What Happens Next?
                                </h6>
                                <div class="row text-start">
                                    <div class="col-md-6">
                                        <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">
                                            <i class="fas fa-envelope me-2"></i>Confirmation email sent
                                        </p>
                                        <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">
                                            <i class="fas fa-user-tie me-2"></i>Technician assignment
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">
                                            <i class="fas fa-sms me-2"></i>SMS notifications
                                        </p>
                                        <p class="mb-2" style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">
                                            <i class="fas fa-tools me-2"></i>Service delivery
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <a href="{{ route('home') }}" class="btn btn-light btn-lg me-3" style="border-radius: 12px; padding: 0.75rem 2rem; font-weight: 600;">
                                    <i class="fas fa-home me-2"></i>Return Home
                                </a>
                                <button type="button" class="btn btn-outline-light btn-lg" style="border-radius: 12px; padding: 0.75rem 2rem; font-weight: 600;" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Print Receipt
                                </button>
                            </div>

                            <div class="mt-4">
                                <small style="color: rgba(255,255,255,0.7);">
                                    <i class="fas fa-phone me-1"></i>
                                    Need help? Call <strong>(555) 123-4567</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let currentStep = 1;
    const totalSteps = 4;
        let selectedTimeSlot = null;

    // Enhanced step navigation with animations
    function nextStep() {
        if (currentStep < totalSteps) {
            // Add slide-out animation to current section
            const currentSection = document.getElementById(`section-${currentStep}`);
            currentSection.classList.add('slide-out');

            setTimeout(() => {
                // Hide current section
                currentSection.classList.remove('active', 'slide-out');
                document.getElementById(`step-${currentStep}`).classList.remove('active');
                document.getElementById(`step-${currentStep}`).classList.add('completed');

                currentStep++;

                // Show next section with animation
                const nextSection = document.getElementById(`section-${currentStep}`);
                nextSection.classList.add('active');
                document.getElementById(`step-${currentStep}`).classList.add('active');

                // Update progress line with animation
                updateProgressLine();

                // Scroll to top of form
                document.querySelector('.appointment-container').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 400);
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            // Add slide-out animation to current section
            const currentSection = document.getElementById(`section-${currentStep}`);
            currentSection.classList.add('slide-out');

            setTimeout(() => {
                // Hide current section
                currentSection.classList.remove('active', 'slide-out');
                document.getElementById(`step-${currentStep}`).classList.remove('active');

                currentStep--;

                // Show previous section with animation
                const prevSection = document.getElementById(`section-${currentStep}`);
                prevSection.classList.add('active');
                document.getElementById(`step-${currentStep}`).classList.add('active');
                document.getElementById(`step-${currentStep}`).classList.remove('completed');

                // Update progress line
                updateProgressLine();

                // Scroll to top of form
                document.querySelector('.appointment-container').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 400);
        }
    }

    function updateProgressLine() {
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        const progressLine = document.querySelector('.step-indicator::after');
        if (progressLine) {
            progressLine.style.width = progress + '%';
        }
    }

        // Initialize date picker
        flatpickr("#appointment_date", {
            minDate: "today",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr) {
                if (document.getElementById('service_type_id').value) {
                    loadAvailableSlots();
                }
            }
        });

        // Service type change handler
        document.getElementById('service_type_id').addEventListener('change', function() {
            if (this.value && document.getElementById('appointment_date').value) {
                loadAvailableSlots();
            }
        });

        // Card number change handler for card type detection
        document.getElementById('card_number').addEventListener('input', function() {
            const cardType = detectCardType(this.value);
            document.getElementById('card_type').value = cardType.charAt(0).toUpperCase() + cardType.slice(1);
        });

        // Card type detection function
        function detectCardType(cardNumber) {
            const cleanNumber = cardNumber.replace(/\D/g, '');

            if (/^4/.test(cleanNumber)) {
                return 'visa';
            } else if (/^5[1-5]/.test(cleanNumber)) {
                return 'mastercard';
            } else if (/^3[47]/.test(cleanNumber)) {
                return 'amex';
            } else if (/^6/.test(cleanNumber)) {
                return 'discover';
            } else {
                return 'unknown';
            }
        }

        // Load available time slots
        function loadAvailableSlots() {
            const serviceId = document.getElementById('service_type_id').value;
            const date = document.getElementById('appointment_date').value;

            if (!serviceId || !date) return;

            fetch(`/available-slots?service_type_id=${serviceId}&date=${date}`)
                .then(response => response.json())
                .then(data => {
                    displayTimeSlots(data);
                })
                .catch(error => {
                    console.error('Error loading time slots:', error);
                });
        }

        // Display time slots
        function displayTimeSlots(data) {
            const container = document.getElementById('time-slots');
            const slots = data.time_slots || [];

            if (slots.length === 0) {
                container.innerHTML = `
                    <div class="col-12 text-center">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Available Time Slots</h6>
                        <p class="text-muted">No technicians are available for ${data.service_type} on ${new Date(data.date).toLocaleDateString()}</p>
                        <p class="text-muted small">Try selecting a different date or service type.</p>
                    </div>`;
                return;
            }

            container.innerHTML = `
                <div class="col-12 mb-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>${data.service_type}</strong> - Estimated duration: <strong>${data.service_duration} minutes</strong>
                    </div>
                </div>
                ${slots.map((slot, index) => `
                    <div class="time-slot"
                         data-datetime="${slot.datetime}"
                         onclick="selectTimeSlot(this, '${slot.datetime}')"
                         style="animation-delay: ${index * 0.1}s">
                        <div class="time-display">
                            <i class="fas fa-clock me-2"></i>
                            ${slot.start_time} - ${slot.end_time}
                        </div>
                    </div>
                `).join('')}
            `;
        }

        // Select time slot
        function selectTimeSlot(element, datetime) {
            // Remove previous selection
            document.querySelectorAll('.time-slot').forEach(slot => slot.classList.remove('selected'));
            element.classList.add('selected');

            selectedTimeSlot = datetime;
            document.getElementById('next-btn-1').disabled = false;

            // Update the hidden input for the selected datetime
            if (!document.getElementById('selected_datetime')) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = 'selected_datetime';
                hiddenInput.name = 'scheduled_at';
                document.getElementById('appointment-form').appendChild(hiddenInput);
            }
            document.getElementById('selected_datetime').value = datetime;
        }

        // Navigation functions
        function nextStep() {
            if (currentStep < 4) {
                if (validateCurrentStep()) {
                    document.getElementById(`section-${currentStep}`).classList.remove('active');
                    currentStep++;
                    document.getElementById(`section-${currentStep}`).classList.add('active');
                    updateStepIndicator();

                    if (currentStep === 3) {
                        updateSummary();
                        populateCustomerInfo();
                    }
                }
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                document.getElementById(`section-${currentStep}`).classList.remove('active');
                currentStep--;
                document.getElementById(`section-${currentStep}`).classList.add('active');
                updateStepIndicator();
            }
        }

        function updateStepIndicator() {
            for (let i = 1; i <= 4; i++) {
                const step = document.getElementById(`step-${i}`);
                if (i < currentStep) {
                    step.classList.add('completed');
                    step.classList.remove('active');
                } else if (i === currentStep) {
                    step.classList.add('active');
                    step.classList.remove('completed');
                } else {
                    step.classList.remove('active', 'completed');
                }
            }
        }

        function validateCurrentStep() {
            if (currentStep === 1) {
                return document.getElementById('service_type_id').value && selectedTimeSlot;
            } else if (currentStep === 2) {
                return document.getElementById('customer_name').value &&
                       document.getElementById('customer_phone').value &&
                       document.getElementById('customer_address').value;
            }
            return true;
        }

        function updateSummary() {
            const serviceSelect = document.getElementById('service_type_id');
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];

            document.getElementById('summary-service').textContent = selectedOption.text;
            document.getElementById('summary-datetime').textContent = `${document.getElementById('appointment_date').value} at ${new Date(selectedTimeSlot).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;

            const rate = parseFloat(selectedOption.dataset.rate);
            const duration = parseInt(selectedOption.dataset.duration);
            const cost = (rate / 60) * duration;
            document.getElementById('summary-cost').textContent = `$${cost.toFixed(2)}`;
        }

        function populateCustomerInfo() {
            // Auto-populate customer info from step 2
            const customerName = document.getElementById('customer_name').value;
            const customerPhone = document.getElementById('customer_phone').value;
            const customerAddress = document.getElementById('customer_address').value;

            if (customerName) {
                const nameParts = customerName.split(' ');
                document.getElementById('first_name').value = nameParts[0] || '';
                document.getElementById('last_name').value = nameParts.slice(1).join(' ') || '';
            }
            document.getElementById('phone').value = customerPhone;
            document.getElementById('address').value = customerAddress;
        }

        function processPayment() {
            const payBtn = document.getElementById('pay-btn');
            payBtn.disabled = true;
            payBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            // Validate all required fields
            const requiredFields = [
                'card_number', 'expiry_month', 'expiry_year', 'cvv',
                'first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip'
            ];

            let isValid = true;
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                payBtn.disabled = false;
                payBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Authorize Payment';
                document.getElementById('card-errors').textContent = 'Please fill in all required fields.';
                return;
            }

            // Clear any previous errors
            document.getElementById('card-errors').textContent = '';

            // Submit the form
            document.getElementById('appointment-form').submit();
        }

        // Google Maps Integration
        let map;
        let marker;
        let autocomplete;
        let geocoder;

        function initGoogleMaps() {
            const apiKey = '{{ config("services.google_maps.api_key") }}';

            console.log('Google Maps API Key:', apiKey ? 'Present' : 'Missing');

            if (!apiKey || apiKey === 'your_google_maps_api_key_here') {
                console.log('No valid API key found, using fallback mode');
                // Fallback: Hide map and show manual input
                document.getElementById('map').style.display = 'none';
                document.querySelector('small.form-text').innerHTML =
                    '<i class="fas fa-info-circle me-1"></i>Please enter your full address manually.';
                return;
            }

            // Check if Google Maps is already loaded
            if (window.google && window.google.maps) {
                console.log('Google Maps already loaded, initializing directly');
                initMap();
                return;
            }

            // Load Google Maps API with marker library
            console.log('Loading Google Maps API...');
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places,marker&callback=initMap`;
            script.async = true;
            script.defer = true;
            script.onerror = function() {
                console.error('Failed to load Google Maps API script');
                document.getElementById('map').style.display = 'none';
                document.querySelector('small.form-text').innerHTML =
                    '<i class="fas fa-exclamation-triangle me-1"></i>Google Maps failed to load. Please enter your address manually.';
            };
            document.head.appendChild(script);
        }

        function initMap() {
            try {
                console.log('Initializing Google Maps...');

                // Check if Google Maps is available
                if (!window.google || !window.google.maps) {
                    throw new Error('Google Maps API not loaded');
                }

                // Initialize map
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 40.7128, lng: -74.0060 }, // Default to NYC
                    zoom: 13,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false
                });

                console.log('Google Maps initialized successfully');
            } catch (error) {
                console.error('Error initializing Google Maps:', error);
                document.getElementById('map').style.display = 'none';
                document.querySelector('small.form-text').innerHTML =
                    '<i class="fas fa-exclamation-triangle me-1"></i>Error loading Google Maps: ' + error.message + '. Please enter your address manually.';
                return;
            }

            // Initialize geocoder
            geocoder = new google.maps.Geocoder();

            // Initialize autocomplete
            const addressInput = document.getElementById('customer_address');
            autocomplete = new google.maps.places.Autocomplete(addressInput, {
                types: ['address'],
                componentRestrictions: { country: 'us' } // Restrict to US addresses
            });

            // Handle autocomplete selection
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    const location = place.geometry.location;
                    map.setCenter(location);
                    map.setZoom(16);

                    // Update marker using AdvancedMarkerElement or fallback to Marker
                    if (marker) {
                        if (marker.map) {
                            marker.map = null;
                        } else {
                            marker.setMap(null);
                        }
                    }

                    try {
                        // Try AdvancedMarkerElement first
                        if (window.google && window.google.maps && window.google.maps.marker && window.google.maps.marker.AdvancedMarkerElement) {
                            marker = new google.maps.marker.AdvancedMarkerElement({
                                position: location,
                                map: map,
                                title: place.formatted_address
                            });
                        } else {
                            // Fallback to regular Marker
                            marker = new google.maps.Marker({
                                position: location,
                                map: map,
                                title: place.formatted_address
                            });
                        }
                    } catch (error) {
                        console.warn('Error creating marker:', error);
                        // Fallback to regular Marker
                        marker = new google.maps.Marker({
                            position: location,
                            map: map,
                            title: place.formatted_address
                        });
                    }

                    // Update hidden fields
                    document.getElementById('address_lat').value = location.lat();
                    document.getElementById('address_lng').value = location.lng();
                    document.getElementById('address_components').value = JSON.stringify(place.address_components);
                }
            });

            // Handle map clicks
            map.addListener('click', function(event) {
                const location = event.latLng;

                // Update marker using AdvancedMarkerElement or fallback to Marker
                if (marker) {
                    if (marker.map) {
                        marker.map = null;
                    } else {
                        marker.setMap(null);
                    }
                }

                try {
                    // Try AdvancedMarkerElement first
                    if (window.google && window.google.maps && window.google.maps.marker && window.google.maps.marker.AdvancedMarkerElement) {
                        marker = new google.maps.marker.AdvancedMarkerElement({
                            position: location,
                            map: map
                        });
                    } else {
                        // Fallback to regular Marker
                        marker = new google.maps.Marker({
                            position: location,
                            map: map
                        });
                    }
                } catch (error) {
                    console.warn('Error creating marker:', error);
                    // Fallback to regular Marker
                    marker = new google.maps.Marker({
                        position: location,
                        map: map
                    });
                }

                // Reverse geocode to get address
                geocoder.geocode({ location: location }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        const address = results[0].formatted_address;
                        document.getElementById('customer_address').value = address;
                        document.getElementById('address_lat').value = location.lat();
                        document.getElementById('address_lng').value = location.lng();
                        document.getElementById('address_components').value = JSON.stringify(results[0].address_components);
                    }
                });
            });

            // Try to geocode existing address if any
            const existingAddress = addressInput.value;
            if (existingAddress) {
                geocoder.geocode({ address: existingAddress }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        const location = results[0].geometry.location;
                        map.setCenter(location);
                        map.setZoom(16);

                        try {
                            // Try AdvancedMarkerElement first
                            if (window.google && window.google.maps && window.google.maps.marker && window.google.maps.marker.AdvancedMarkerElement) {
                                marker = new google.maps.marker.AdvancedMarkerElement({
                                    position: location,
                                    map: map,
                                    title: existingAddress
                                });
                            } else {
                                // Fallback to regular Marker
                                marker = new google.maps.Marker({
                                    position: location,
                                    map: map,
                                    title: existingAddress
                                });
                            }
                        } catch (error) {
                            console.warn('Error creating marker:', error);
                            // Fallback to regular Marker
                            marker = new google.maps.Marker({
                                position: location,
                                map: map,
                                title: existingAddress
                            });
                        }
                    }
                });
            }
        }

        // Initialize Google Maps when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initGoogleMaps();
        });
    </script>
@endpush
