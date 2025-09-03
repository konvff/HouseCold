@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')
@section('breadcrumb', 'Admin Dashboard')

@section('content')

    <div class="container mt-4">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-white bg-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar fa-3x mb-3"></i>
                        <h4>{{ $totalAppointments ?? 0 }}</h4>
                        <p class="mb-0">Total Appointments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-white bg-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-3x mb-3"></i>
                        <h4>{{ $pendingAppointments ?? 0 }}</h4>
                        <p class="mb-0">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-white bg-info">
                    <div class="card-body text-center">
                        <i class="fas fa-user-tie fa-3x mb-3"></i>
                        <h4>{{ $totalTechnicians ?? 0 }}</h4>
                        <p class="mb-0">Technicians</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-white bg-success">
                    <div class="card-body text-center">
                        <i class="fas fa-tools fa-3x mb-3"></i>
                        <h4>{{ $totalServices ?? 0 }}</h4>
                        <p class="mb-0">Service Types</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="quick-actions">
                    <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('service-types.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i>Add Service Type
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('technicians.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-user-plus me-2"></i>Add Technician
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('appointments.index') }}" class="btn btn-info w-100">
                                <i class="fas fa-list me-2"></i>View Appointments
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('technicians.index') }}" class="btn btn-warning w-100">
                                <i class="fas fa-users me-2"></i>Manage Technicians
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card stats-card">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar-check me-2"></i>Recent Appointments</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($recentAppointments) && $recentAppointments->count() > 0)
                            @foreach($recentAppointments as $appointment)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                    <div>
                                        <strong>{{ $appointment->customer_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $appointment->serviceType->name }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $appointment->status === 'pending' ? 'warning' : ($appointment->status === 'confirmed' ? 'info' : 'success') }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $appointment->scheduled_at->format('M j, g:i A') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No recent appointments</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card stats-card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-line me-2"></i>System Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Appointment Status Distribution</h6>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" style="width: {{ $pendingPercentage ?? 0 }}%">
                                    Pending: {{ $pendingPercentage ?? 0 }}%
                                </div>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-info" style="width: {{ $confirmedPercentage ?? 0 }}%">
                                    Confirmed: {{ $confirmedPercentage ?? 0 }}%
                                </div>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-success" style="width: {{ $completedPercentage ?? 0 }}%">
                                    Completed: {{ $completedPercentage ?? 0 }}%
                                </div>
                            </div>
                        </div>

                        <div class="row text-center">
                            <div class="col-6">
                                <h6 class="text-primary">{{ $todayAppointments ?? 0 }}</h6>
                                <small class="text-muted">Today's Appointments</small>
                            </div>
                            <div class="col-6">
                                <h6 class="text-success">{{ $weekAppointments ?? 0 }}</h6>
                                <small class="text-muted">This Week</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
