@extends('layouts.dashboard')

@section('title', 'Technician Dashboard')
@section('breadcrumb', 'Technician Dashboard')
@section('content')
<style>
/* Modern Dashboard Styling */
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 2rem;
    margin-bottom: 2rem;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    position: relative;
    overflow: hidden;
    animation: slideInFromTop 0.8s ease-out;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(50%, -50%);
    animation: float 6s ease-in-out infinite;
}

.dashboard-header::after {
    content: '';
    position: absolute;
    bottom: -50px;
    left: -50px;
    width: 150px;
    height: 150px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
    animation: float 8s ease-in-out infinite reverse;
}

/* Floating animation for decorative elements */
@keyframes float {
    0%, 100% { transform: translate(50%, -50%) scale(1); }
    50% { transform: translate(50%, -50%) scale(1.1); }
}

.dashboard-header::after {
    animation: floatReverse 8s ease-in-out infinite;
}

@keyframes floatReverse {
    0%, 100% { transform: translate(-50%, 50%) scale(1); }
    50% { transform: translate(-50%, 50%) scale(1.1); }
}

/* Slide in animation for header */
@keyframes slideInFromTop {
    0% {
        opacity: 0;
        transform: translateY(-30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.welcome-content {
    position: relative;
    z-index: 2;
    animation: fadeInLeft 1s ease-out 0.3s both;
}

.welcome-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    animation: slideInLeft 1s ease-out 0.5s both;
}

.welcome-subtitle {
    font-size: 1.1rem;
    opacity: 0.95;
    font-weight: 400;
    line-height: 1.5;
    animation: slideInLeft 1s ease-out 0.7s both;
}

/* Welcome content animations */
@keyframes fadeInLeft {
    0% {
        opacity: 0;
        transform: translateX(-30px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInLeft {
    0% {
        opacity: 0;
        transform: translateX(-20px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

.header-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1rem;
    position: relative;
    z-index: 2;
    animation: fadeInRight 1s ease-out 0.4s both;
}

.availability-btn-wrapper {
    text-align: center;
    animation: bounceIn 1s ease-out 0.8s both;
}

/* Header actions animations */
@keyframes fadeInRight {
    0% {
        opacity: 0;
        transform: translateX(30px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
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

.availability-btn-wrapper .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-width: 2px;
    border-radius: 12px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
}

.availability-btn-wrapper .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.3);
}

.user-info {
    display: flex;
    align-items: center;
    background: rgba(255,255,255,0.1);
    padding: 1rem 1.5rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    animation: slideInUp 1s ease-out 1s both;
}

/* User info animation */
@keyframes slideInUp {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.user-avatar-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-details {
    text-align: left;
}

.user-phone {
    font-size: 1rem;
    margin-bottom: 0.25rem;
    color: white;
}

.user-email {
    font-size: 0.9rem;
    opacity: 0.9;
    color: white;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    animation: fadeInUp 1s ease-out 1.2s both;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: scaleIn 0.6s ease-out both;
}

/* Stagger animation for stat cards */
.stat-card:nth-child(1) { animation-delay: 1.3s; }
.stat-card:nth-child(2) { animation-delay: 1.4s; }
.stat-card:nth-child(3) { animation-delay: 1.5s; }
.stat-card:nth-child(4) { animation-delay: 1.6s; }
.stat-card:nth-child(5) { animation-delay: 1.7s; }

/* Stats animations */
@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes scaleIn {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.stat-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    animation: pulse 2s ease-in-out infinite;
}

.stat-icon i {
    transition: transform 0.3s ease;
}

.stat-card:hover .stat-icon i {
    transform: scale(1.1);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 0.5rem;
    animation: countUp 1s ease-out 0.5s both;
}

.stat-label {
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    animation: fadeIn 1s ease-out 0.7s both;
}

/* Stat card specific colors */
.stat-card.pending .stat-icon i { color: #6c757d; }
.stat-card.confirmed .stat-icon i { color: #28a745; }
.stat-card.in-progress .stat-icon i { color: #ffc107; }
.stat-card.completed .stat-icon i { color: #17a2b8; }
.stat-card.availability .stat-icon i { color: #6610f2; }

/* Additional animations */
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes countUp {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

/* Job Cards Styling */
.job-section {
    margin-bottom: 3rem;
    animation: slideInUp 1s ease-out 1.8s both;
}

.job-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    animation: slideInUp 0.8s ease-out both;
}

/* Stagger animation for job cards */
.job-section:nth-child(1) { animation-delay: 1.8s; }
.job-section:nth-child(2) { animation-delay: 2s; }
.job-section:nth-child(3) { animation-delay: 2.2s; }

.job-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.job-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.job-card .card-header h5 {
    margin: 0;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.job-card .card-header p {
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

.job-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
    animation: slideInRight 0.6s ease-out both;
    animation-delay: calc(0.1s * var(--item-index, 0));
}

/* Stagger animation for job items */
.job-item:nth-child(1) { --item-index: 1; }
.job-item:nth-child(2) { --item-index: 2; }
.job-item:nth-child(3) { --item-index: 3; }
.job-item:nth-child(4) { --item-index: 4; }
.job-item:nth-child(5) { --item-index: 5; }

@keyframes slideInRight {
    0% {
        opacity: 0;
        transform: translateX(30px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

.job-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 1rem 0;
}

.job-detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.job-detail i {
    color: #667eea;
    width: 16px;
    text-align: center;
}

.job-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.btn-modern {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-modern:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.btn-modern:active {
    transform: translateY(-1px) scale(0.98);
}

/* Button entrance animations */
.btn-modern {
    animation: buttonSlideUp 0.5s ease-out both;
    animation-delay: calc(0.2s * var(--btn-index, 0));
}

@keyframes buttonSlideUp {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.btn-start {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.btn-stop {
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    color: white;
}

.btn-pause {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
}

.btn-accept {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.btn-decline {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: white;
}

.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s ease-in-out infinite;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h5 {
    margin-bottom: 0.5rem;
    color: #495057;
}

.empty-state p {
    margin-bottom: 0.5rem;
}

.empty-state small {
    font-size: 0.85rem;
}

.job-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.job-title {
    margin: 0;
    font-weight: 600;
    color: #2c3e50;
}

.job-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.job-status.pending {
    background: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
}

.job-status.confirmed {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.job-status.in-progress {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.job-status.completed {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.job-item.confirmed {
    border-left-color: #28a745;
}

.job-item.in-progress {
    border-left-color: #ffc107;
}

.job-item.completed {
    border-left-color: #17a2b8;
}

.job-item.pending {
    border-left-color: #6c757d;
}

.job-item:hover {
    background: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateX(5px);
}

.job-item.pending {
    border-left-color: #ffc107;
}

.job-item.confirmed {
    border-left-color: #17a2b8;
}

.job-item.in-progress {
    border-left-color: #007bff;
}

.job-item.completed {
    border-left-color: #28a745;
}

.job-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.job-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.job-status {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.job-status.pending {
    background: #fff3cd;
    color: #856404;
}

.job-status.confirmed {
    background: #d1ecf1;
    color: #0c5460;
}

.job-status.in-progress {
    background: #cce5ff;
    color: #004085;
}

.job-status.completed {
    background: #d4edda;
    color: #155724;
}

.job-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.job-detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.job-detail i {
    color: #667eea;
    width: 16px;
}

.job-detail span {
    color: #6c757d;
    font-size: 0.9rem;
}

.job-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-modern {
    border-radius: 25px;
    padding: 0.5rem 1.2rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.8rem;
    border: none;
    transition: all 0.3s ease;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-accept {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.btn-decline {
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    color: white;
}

.btn-start {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

.btn-stop {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.btn-pause {
    background: linear-gradient(135deg, #ffc107, #e0a800);
    color: white;
}

/* Timer Styling */
.timer-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 1rem;
}

.timer-display {
    font-size: 2rem;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    margin-bottom: 0.5rem;
}

.timer-label {
    font-size: 0.9rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Empty State Styling */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    font-size: 1.1rem;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-stats {
        grid-template-columns: 1fr;
    }

    .job-details {
        grid-template-columns: 1fr;
    }

    .job-actions {
        justify-content: center;
    }
}

/* Toast Styling */
.toast-container {
    z-index: 1055;
}

.toast {
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.toast-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px 12px 0 0;
}

/* Loading Animation */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="container-fluid">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-6">
                <div class="welcome-content">
                    <h1 class="welcome-title mb-3">
                        <i class="fas fa-tools me-3"></i>
                        Welcome back, {{ Auth::user()->name }}!
                    </h1>
                    <p class="welcome-subtitle mb-0">Manage your appointments and track your work progress</p>
                </div>
            </div>
            <div class="col-lg-5 col-md-6">
                <div class="header-actions">
                    <div class="availability-btn-wrapper mb-3">
                        <a href="{{ route('technician-availabilities.my-availability') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-calendar-alt me-2"></i>Manage Availability
                        </a>
                    </div>
                    <div class="user-info">
                        <div class="user-avatar-wrapper me-3">
                            <i class="fas fa-user-circle fa-2x"></i>
                        </div>
                        <div class="user-details">
                            <div class="user-phone fw-bold">{{ Auth::user()->technician->phone ?? 'No phone' }}</div>
                            <div class="user-email">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Stats -->
    <div class="dashboard-stats">
        <div class="stat-card pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">{{ $pendingAppointments->count() }}</div>
            <div class="stat-label">Pending Jobs</div>
        </div>
        <div class="stat-card confirmed">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-number">{{ $confirmedAppointments->count() }}</div>
            <div class="stat-label">Confirmed Jobs</div>
        </div>
        <div class="stat-card in-progress">
            <div class="stat-icon">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-number">{{ $confirmedAppointments->where('status', 'in_progress')->count() }}</div>
            <div class="stat-label">In Progress</div>
        </div>
        <div class="stat-card completed">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">{{ $completedAppointments->count() }}</div>
            <div class="stat-label">Completed Today</div>
        </div>
        <div class="stat-card availability">
            <div class="stat-icon">
                <a href="{{ route('technician-availabilities.my-availability') }}" class="text-decoration-none">
                    <i class="fas fa-calendar-alt"></i>
                </a>
            </div>
            <div class="stat-number">-</div>
            <div class="stat-label">Manage Availability</div>
        </div>
    </div>

    <!-- Pending Appointments -->
    <div class="job-section">
        <div class="job-card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-clock me-2"></i>
                    Pending Appointments
                </h5>
                <p>New appointment requests waiting for your response</p>
                    </div>
                    <div class="card-body">
                        @if($pendingAppointments->count() > 0)
                            @foreach($pendingAppointments as $appointment)
                        <div class="job-item pending" id="pending-{{ $appointment->id }}">
                            <div class="job-header">
                                <h6 class="job-title">{{ $appointment->serviceType->name }}</h6>
                                <span class="job-status pending">Pending</span>
                            </div>

                            <div class="job-details">
                                <div class="job-detail">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ $appointment->scheduled_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-user"></i>
                                    <span>{{ $appointment->customer_name }}</span>
                                    </div>
                                <div class="job-detail">
                                    <i class="fas fa-phone"></i>
                                    <span>{{ $appointment->customer_phone }}</span>
                                        </div>
                                <div class="job-detail">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ Str::limit($appointment->customer_address, 40) }}</span>
                                    </div>
                                <div class="job-detail">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>Est: ${{ number_format($appointment->estimated_cost, 2) }}</span>
                                        </div>
                                    </div>

                            <div class="job-actions">
                                            <form method="POST" action="{{ route('appointments.accept', $appointment) }}" class="d-inline">
                                                @csrf
                                    <button type="submit" class="btn btn-modern btn-accept">
                                        <i class="fas fa-check me-1"></i>Accept Job
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('appointments.decline', $appointment) }}" class="d-inline">
                                                @csrf
                                    <button type="submit" class="btn btn-modern btn-decline">
                                                    <i class="fas fa-times me-1"></i>Decline
                                                </button>
                                            </form>
                                    </div>
                                </div>
                            @endforeach
                        @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No pending appointments at the moment</p>
                        <small class="text-muted">New requests will appear here automatically</small>
                            </div>
                        @endif
                </div>
            </div>
        </div>

    <!-- Confirmed & In-Progress Appointments -->
    <div class="job-section">
        <div class="job-card">
                    <div class="card-header">
                <h5>
                    <i class="fas fa-calendar-check me-2"></i>
                    Active Jobs
                </h5>
                <p>Your confirmed and in-progress appointments</p>
                    </div>
                    <div class="card-body">
                        @if($confirmedAppointments->count() > 0)
                            @foreach($confirmedAppointments as $appointment)
                        <div class="job-item {{ $appointment->status === 'confirmed' ? 'confirmed' : 'in-progress' }}"
                             id="appointment-{{ $appointment->id }}">
                            <div class="job-header">
                                <h6 class="job-title">{{ $appointment->serviceType->name }}</h6>
                                @if($appointment->status === 'confirmed')
                                    <span class="job-status confirmed">Confirmed</span>
                                @else
                                    <span class="job-status in-progress">In Progress</span>
                                @endif
                            </div>

                            <div class="job-details">
                                <div class="job-detail">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ $appointment->scheduled_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-user"></i>
                                    <span>{{ $appointment->customer_name }}</span>
                                    </div>
                                <div class="job-detail">
                                    <i class="fas fa-phone"></i>
                                    <span>{{ $appointment->customer_phone }}</span>
                                        </div>
                                <div class="job-detail">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ Str::limit($appointment->customer_address, 40) }}</span>
                                    </div>
                                <div class="job-detail">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>Est: ${{ number_format($appointment->estimated_cost, 2) }}</span>
                                        </div>
                                    </div>

                                        @if($appointment->status === 'confirmed')
                                <div class="job-actions">
                                                                        <button type="button" class="btn btn-modern btn-start" onclick="startTimer({{ $appointment->id }}, event)">
                                        <i class="fas fa-play me-1"></i>Start Work
                                    </button>
                                            </div>
                                        @elseif($appointment->status === 'in_progress')
                                            @php
                                                $currentTimer = $appointment->getCurrentTimeLog();
                                            @endphp
                                            @if($currentTimer)
                                    <div class="timer-container">
                                        <div class="timer-display" id="elapsed-{{ $appointment->id }}">00:00:00</div>
                                        <div class="timer-label">Work in progress</div>
                                                </div>
                                    <div class="job-actions">
                                                                                <button type="button" class="btn btn-modern btn-stop" onclick="stopTimer({{ $appointment->id }}, event)">
                                            <i class="fas fa-stop me-1"></i>Complete Work
                                        </button>
                                        <button type="button" class="btn btn-modern btn-pause" onclick="pauseTimer({{ $appointment->id }}, event)">
                                            <i class="fas fa-pause me-1"></i>Pause
                                    </button>
                                                </div>
                                            @endif
                                        @endif
                                </div>
                            @endforeach
                        @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>No active jobs at the moment</p>
                        <small class="text-muted">Accepted appointments will appear here</small>
                            </div>
                        @endif
                </div>
            </div>
        </div>

        <!-- Completed Appointments -->
    <div class="job-section">
        <div class="job-card">
                    <div class="card-header">
                <h5>
                    <i class="fas fa-check-circle me-2"></i>
                    Recent Completed Jobs
                </h5>
                <p>Your completed work history and earnings</p>
                    </div>
                    <div class="card-body">
                        @if($completedAppointments->count() > 0)
                            @foreach($completedAppointments as $appointment)
                        <div class="job-item completed">
                            <div class="job-header">
                                <h6 class="job-title">{{ $appointment->serviceType->name }}</h6>
                                <span class="job-status completed">Completed</span>
                            </div>

                            <div class="job-details">
                                <div class="job-detail">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ $appointment->scheduled_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-user"></i>
                                    <span>{{ $appointment->customer_name }}</span>
                                    </div>
                                <div class="job-detail">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ Str::limit($appointment->customer_address, 40) }}</span>
                                    </div>
                                        @if($appointment->actual_cost)
                                    <div class="job-detail">
                                        <i class="fas fa-dollar-sign"></i>
                                        <span>Final: ${{ number_format($appointment->actual_cost, 2) }}</span>
                                    </div>
                                @endif
                                        @if($appointment->timeLogs->count() > 0)
                                            @php
                                                $totalTime = $appointment->timeLogs->sum('duration_minutes');
                                                $hours = floor($totalTime / 60);
                                                $minutes = $totalTime % 60;
                                            @endphp
                                    <div class="job-detail">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $hours }}h {{ $minutes }}m</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                    <div class="empty-state">
                        <i class="fas fa-history"></i>
                        <p>No completed jobs yet</p>
                        <small class="text-muted">Completed appointments will appear here</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="timerToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-clock me-2"></i>
            <strong class="me-auto">Job Status</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="timerToastBody">
            <!-- Status message will appear here -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Enhanced focus states for accessibility */
.btn-modern:focus,
.btn:focus {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}

/* Hover effects for interactive elements */
.job-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
}

/* Pulse animation for notifications */
@keyframes notificationPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.notification-badge {
    animation: notificationPulse 2s ease-in-out infinite;
}
</style>

<script>
// Global timer variables
let activeTimers = {};
let timerIntervals = {};

// Initialize timers on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize timers
    updateTimers();

    // Add entrance animations for elements
    addEntranceAnimations();

    // Add scroll-triggered animations
    addScrollAnimations();
});

// Add entrance animations for elements
function addEntranceAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all job items and cards
    document.querySelectorAll('.job-item, .stat-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease-out';
        observer.observe(el);
    });
}

// Add scroll-triggered animations
function addScrollAnimations() {
    let ticking = false;

    function updateOnScroll() {
        if (!ticking) {
            requestAnimationFrame(() => {
                const scrolled = window.pageYOffset;
                const parallax = scrolled * 0.5;

                // Parallax effect for dashboard header
                const header = document.querySelector('.dashboard-header');
                if (header) {
                    header.style.transform = `translateY(${parallax * 0.1}px)`;
                }

                ticking = false;
            });
            ticking = true;
        }
    }

    window.addEventListener('scroll', updateOnScroll);
}

    // Timer functionality for in-progress appointments
    function updateTimers() {
        @foreach($confirmedAppointments as $appointment)
            @if($appointment->status === 'in_progress')
                @php
                    $currentTimer = $appointment->getCurrentTimeLog();
                @endphp
                @if($currentTimer)
                startTimerDisplay({{ $appointment->id }}, '{{ $currentTimer->started_at }}');
            @endif
            @endif
        @endforeach
    }

// Start timer display for a specific appointment
function startTimerDisplay(appointmentId, startTime) {
    if (activeTimers[appointmentId]) return; // Already running

    const start = new Date(startTime);
    activeTimers[appointmentId] = start;

    // Update immediately
    updateTimerDisplay(appointmentId);

    // Then update every second
    timerIntervals[appointmentId] = setInterval(() => {
        updateTimerDisplay(appointmentId);
    }, 1000);
}

// Update timer display
function updateTimerDisplay(appointmentId) {
    const start = activeTimers[appointmentId];
    if (!start) return;

    const now = new Date();
    const elapsed = Math.floor((now - start) / 1000);

    const hours = Math.floor(elapsed / 3600);
    const minutes = Math.floor((elapsed % 3600) / 60);
    const seconds = elapsed % 60;

    const display = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

    const timerElement = document.getElementById(`elapsed-${appointmentId}`);
    if (timerElement) {
        timerElement.textContent = display;
    }
}

// Start timer
function startTimer(appointmentId, event) {
    console.log('Starting timer for appointment:', appointmentId);
    const button = event.target;
    const originalText = button.innerHTML;

    // Show loading state
    button.innerHTML = '<span class="loading-spinner me-2"></span>Starting...';
    button.disabled = true;

    fetch(`/appointments/${appointmentId}/start-timer`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Timer started successfully!', 'success');
            updateAppointmentRow(appointmentId, 'in_progress');
            startTimerDisplay(appointmentId, data.started_at);
        } else {
            showToast(data.error || 'Failed to start timer', 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to start timer', 'error');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Stop timer
function stopTimer(appointmentId, event) {
    const button = event.target;
    const originalText = button.innerHTML;

    // Show loading state
    button.innerHTML = '<span class="loading-spinner me-2"></span>Completing...';
    button.disabled = true;

    fetch(`/appointments/${appointmentId}/stop-timer`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Work completed successfully!', 'success');
            stopTimerDisplay(appointmentId);
            moveToCompleted(appointmentId, data);
        } else {
            showToast(data.error || 'Failed to stop timer', 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to stop timer', 'error');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Pause timer
function pauseTimer(appointmentId, event) {
    const button = event.target;
    const originalText = button.innerHTML;

    // Show loading state
    button.innerHTML = '<span class="loading-spinner me-2"></span>Pausing...';
    button.disabled = true;

    fetch(`/appointments/${appointmentId}/pause-timer`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Timer paused successfully!', 'success');
            stopTimerDisplay(appointmentId);
            updateAppointmentRow(appointmentId, 'confirmed');
        } else {
            showToast(data.error || 'Failed to pause timer', 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to pause timer', 'error');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Stop timer display
function stopTimerDisplay(appointmentId) {
    if (timerIntervals[appointmentId]) {
        clearInterval(timerIntervals[appointmentId]);
        delete timerIntervals[appointmentId];
    }
    delete activeTimers[appointmentId];
}

// Update appointment row status
function updateAppointmentRow(appointmentId, status) {
    const row = document.getElementById(`appointment-${appointmentId}`);
    if (!row) return;

    // Update status badge
    const statusBadge = row.querySelector('.job-status');
    if (statusBadge) {
        statusBadge.textContent = status === 'in_progress' ? 'In Progress' : 'Confirmed';
        statusBadge.className = `job-status ${status === 'in_progress' ? 'in-progress' : 'confirmed'}`;
    }

    // Update row class
    row.className = `job-item ${status === 'in_progress' ? 'in-progress' : 'confirmed'}`;

    // Update actions based on status
    const actionsContainer = row.querySelector('.job-actions');
    if (actionsContainer) {
        if (status === 'confirmed') {
            actionsContainer.innerHTML = `
                <button type="button" class="btn btn-modern btn-start" onclick="startTimer(${appointmentId}, event)">
                    <i class="fas fa-play me-1"></i>Start Work
                </button>
            `;
        } else if (status === 'in_progress') {
            actionsContainer.innerHTML = `
                <div class="timer-container">
                    <div class="timer-display" id="elapsed-${appointmentId}">00:00:00</div>
                    <div class="timer-label">Work in progress</div>
                </div>
                <div class="job-actions">
                    <button type="button" class="btn btn-modern btn-stop" onclick="stopTimer(${appointmentId}, event)">
                        <i class="fas fa-stop me-1"></i>Complete Work
                    </button>
                    <button type="button" class="btn btn-modern btn-pause" onclick="pauseTimer(${appointmentId}, event)">
                        <i class="fas fa-pause me-1"></i>Pause
                    </button>
                </div>
            `;
        }
    }
}

// Move appointment to completed section
function moveToCompleted(appointmentId, data) {
    const row = document.getElementById(`appointment-${appointmentId}`);
    if (!row) return;

    // Remove from confirmed section
    row.remove();

    // Add to completed section
    const completedSection = document.querySelector('.completed-appointments .card-body');
    if (completedSection) {
        const completedItem = document.createElement('div');
        completedItem.className = 'job-item completed';
        completedItem.innerHTML = `
            <div class="job-header">
                <h6 class="job-title">${row.querySelector('.job-title').textContent}</h6>
                <span class="job-status completed">Completed</span>
            </div>
            <div class="job-details">
                <div class="job-detail">
                    <i class="fas fa-calendar"></i>
                    <span>${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' })}</span>
                </div>
                <div class="job-detail">
                    <i class="fas fa-user"></i>
                    <span>${row.querySelector('.job-detail:nth-child(2) span').textContent}</span>
                </div>
                <div class="job-detail">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Final: $${parseFloat(data.actual_cost).toFixed(2)}</span>
                </div>
                <div class="job-detail">
                    <i class="fas fa-clock"></i>
                    <span>${Math.floor(data.duration_minutes / 60)}h ${data.duration_minutes % 60}m</span>
                </div>
            </div>
        `;

        completedSection.insertBefore(completedItem, completedSection.firstChild);

        // Remove empty state if it exists
        const emptyState = completedSection.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }
    }

    // Update stats
    updateDashboardStats();
}

// Update dashboard stats
function updateDashboardStats() {
    // This could be enhanced to make AJAX calls to update real-time stats
    // For now, we'll just update the UI based on what we know
    const pendingCount = document.querySelectorAll('.pending-appointments .job-item').length;
    const confirmedCount = document.querySelectorAll('.confirmed-appointments .job-item.confirmed').length;
    const inProgressCount = document.querySelectorAll('.confirmed-appointments .job-item.in-progress').length;
    const completedCount = document.querySelectorAll('.completed-appointments .job-item').length;

    // Update stat numbers
    const statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers[0]) statNumbers[0].textContent = pendingCount;
    if (statNumbers[1]) statNumbers[1].textContent = confirmedCount;
    if (statNumbers[2]) statNumbers[2].textContent = inProgressCount;
    if (statNumbers[3]) statNumbers[3].textContent = completedCount;
}

// Show toast notification
function showToast(message, type = 'info') {
    const toastBody = document.getElementById('timerToastBody');
    const toast = document.getElementById('timerToast');

    if (toastBody && toast) {
        toastBody.textContent = message;

        // Add type-specific styling
        toast.className = `toast ${type === 'success' ? 'bg-success text-white' : type === 'error' ? 'bg-danger text-white' : ''}`;

        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
}

// Auto-refresh pending appointments every 30 seconds
setInterval(() => {
    // This could be enhanced to make AJAX calls to check for new pending appointments
    // For now, we'll just log that we're checking
    console.log('Checking for new pending appointments...');
}, 30000);
</script>
@endpush
