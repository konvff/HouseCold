@extends('layouts.dashboard')

@section('title', 'Technician Dashboard')
@section('breadcrumb', 'Technician Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-tie fa-3x text-primary mb-3"></i>
                    <h2>Welcome back, {{ Auth::user()->name }}!</h2>
                    <p class="lead text-muted">Manage your work schedule and appointments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-2x text-primary mb-3"></i>
                    <h4>{{ $totalAppointments ?? 0 }}</h4>
                    <p class="text-muted mb-0">Total Jobs</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-warning mb-3"></i>
                    <h4>{{ $pendingAppointments ?? 0 }}</h4>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-tools fa-2x text-info mb-3"></i>
                    <h4>{{ $inProgressAppointments ?? 0 }}</h4>
                    <p class="text-muted mb-0">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                    <h4>{{ $completedAppointments ?? 0 }}</h4>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Appointments -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-day me-2"></i>Today's Appointments
                    </h5>
                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary btn-sm">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($todayAppointments) && $todayAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Time</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayAppointments as $appointment)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $appointment->customer_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $appointment->customer_phone }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-tools text-primary me-2"></i>
                                                    <div>
                                                        <strong>{{ $appointment->serviceType->name }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $appointment->scheduled_at->format('g:i A') }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ Str::limit($appointment->customer_address, 30) }}</span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-{{ $appointment->status }}">
                                                    {{ $appointment->status->label() }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('appointments.show', $appointment) }}"
                                                       class="btn btn-outline-primary btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($appointment->status === 'confirmed')
                                                        <form method="POST" action="{{ route('appointments.start-timer', $appointment) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-success btn-sm"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Start Work">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($appointment->status === 'in_progress')
                                                        <form method="POST" action="{{ route('appointments.stop-timer', $appointment) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Complete Work">
                                                                <i class="fas fa-stop"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No appointments today</h5>
                            <p class="text-muted">You have a free day! Check your availability settings.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('time-logs.current') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-stopwatch fa-2x mb-2"></i>
                                <span>Time Tracker</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('technician-availabilities.my-availability') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <span>My Availability</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('appointments.index') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-list fa-2x mb-2"></i>
                                <span>All Appointments</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-cog fa-2x mb-2"></i>
                                <span>Settings</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
