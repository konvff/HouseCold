@extends('layouts.dashboard')

@section('title', 'Customer Dashboard')
@section('breadcrumb', 'Customer Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-home fa-3x text-primary mb-3"></i>
                    <h2>Welcome back, {{ Auth::user()->name }}!</h2>
                    <p class="lead text-muted">Manage your house call appointments and services</p>
                    <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Book New Appointment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-2x text-success mb-3"></i>
                    <h4>{{ $totalAppointments ?? 0 }}</h4>
                    <p class="text-muted mb-0">Total Appointments</p>
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
                    <h4>{{ $confirmedAppointments ?? 0 }}</h4>
                    <p class="text-muted mb-0">Confirmed</p>
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

    <!-- Recent Appointments -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Recent Appointments
                    </h5>
                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary btn-sm">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($recentAppointments) && $recentAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Date & Time</th>
                                        <th>Technician</th>
                                        <th>Status</th>
                                        <th>Cost</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAppointments as $appointment)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-tools text-primary me-2"></i>
                                                    <div>
                                                        <strong>{{ $appointment->serviceType->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $appointment->customer_address }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $appointment->scheduled_at->format('M j, Y') }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $appointment->scheduled_at->format('g:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($appointment->technician)
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user-tie text-info me-2"></i>
                                                        <div>
                                                            <strong>{{ $appointment->technician->user->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $appointment->technician->phone }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="status-badge status-{{ $appointment->status }}">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($appointment->actual_cost)
                                                    <strong class="text-success">${{ $appointment->actual_cost }}</strong>
                                                @else
                                                    <span class="text-muted">Est: ${{ $appointment->estimated_cost }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('appointments.show', $appointment) }}"
                                                       class="btn btn-outline-primary btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($appointment->status === 'pending')
                                                        <a href="{{ route('appointments.edit', $appointment) }}"
                                                           class="btn btn-outline-warning btn-sm"
                                                           data-bs-toggle="tooltip"
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
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
                            <h5 class="text-muted">No appointments yet</h5>
                            <p class="text-muted">Book your first house call appointment to get started!</p>
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Book Appointment
                            </a>
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
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                <span>Book Appointment</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-list fa-2x mb-2"></i>
                                <span>View All</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="fas fa-history fa-2x mb-2"></i>
                                <span>Service History</span>
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
