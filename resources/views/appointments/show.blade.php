@extends('layouts.dashboard')

@section('title', 'Appointment Details')
@section('breadcrumb', 'Appointment Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Appointment Details
                    </h2>
                    <p class="text-muted mb-0">View appointment information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Details -->
    <div class="row">
        <div class="col-md-8">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">Appointment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Customer Name</label>
                            <p class="mb-0">{{ $appointment->customer_name }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div>
                                <span class="status-badge status-{{ $appointment->status }}">
                                    {{ $appointment->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Customer Email</label>
                            <p class="mb-0">{{ $appointment->customer_email }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Customer Phone</label>
                            <p class="mb-0">{{ $appointment->customer_phone }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Customer Address</label>
                        <p class="mb-0">{{ $appointment->customer_address }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Service Type</label>
                            <p class="mb-0">
                                <i class="fas fa-tools text-primary me-2"></i>
                                {{ $appointment->serviceType->name }}
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Scheduled Date & Time</label>
                            <p class="mb-0">
                                <i class="fas fa-calendar text-info me-2"></i>
                                {{ $appointment->scheduled_at->format('M j, Y g:i A') }}
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estimated Cost</label>
                            <p class="mb-0">
                                <span class="badge bg-success fs-6">${{ $appointment->estimated_cost }}</span>
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Actual Cost</label>
                            <p class="mb-0">
                                @if($appointment->actual_cost)
                                    <span class="badge bg-success fs-6">${{ $appointment->actual_cost }}</span>
                                @else
                                    <span class="text-muted">Not yet determined</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Created</label>
                            <p class="mb-0">{{ $appointment->created_at->format('M j, Y g:i A') }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <p class="mb-0">{{ $appointment->updated_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Technician Information -->
            <div class="card dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>Assigned Technician
                    </h5>
                </div>
                <div class="card-body">
                    @if($appointment->technician)
                        <div class="text-center mb-3">
                            <i class="fas fa-user-tie fa-3x text-info mb-2"></i>
                            <h5>{{ $appointment->technician->user->name }}</h5>
                            <p class="text-muted mb-0">{{ $appointment->technician->user->email }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <p class="mb-0">{{ $appointment->technician->phone }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $appointment->technician->status === 'active' ? 'success' : ($appointment->technician->status === 'inactive' ? 'secondary' : 'warning') }}">
                                    {{ $appointment->technician->status->label() }}
                                </span>
                            </p>
                        </div>

                        @if($appointment->technician->hourly_rate)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hourly Rate</label>
                                <p class="mb-0">
                                    <span class="badge bg-success">${{ $appointment->technician->hourly_rate }}/hr</span>
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No Technician Assigned</h6>
                            <p class="text-muted small">This appointment is waiting for a technician to be assigned.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Appointment
                        </a>

                        @if(!$appointment->technician && $appointment->status === 'pending')
                            <button class="btn btn-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#assignModal">
                                <i class="fas fa-user-plus me-2"></i>Assign Technician
                            </button>
                        @endif

                        @if($appointment->status === 'confirmed' && $appointment->technician)
                            <form method="POST" action="{{ route('appointments.start-timer', $appointment) }}" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-play me-2"></i>Start Work
                                </button>
                            </form>
                        @endif

                        @if($appointment->status === 'in_progress')
                            <form method="POST" action="{{ route('appointments.stop-timer', $appointment) }}" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-stop me-2"></i>Complete Work
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Logs -->
    @if($appointment->timeLogs->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Work Time Logs
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Duration</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointment->timeLogs as $timeLog)
                                        <tr>
                                            <td>{{ $timeLog->started_at->format('M j, Y') }}</td>
                                            <td>{{ $timeLog->started_at->format('g:i A') }}</td>
                                            <td>
                                                @if($timeLog->ended_at)
                                                    {{ $timeLog->ended_at->format('g:i A') }}
                                                @else
                                                    <span class="text-warning">In Progress</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($timeLog->duration_minutes)
                                                    {{ floor($timeLog->duration_minutes / 60) }}h {{ $timeLog->duration_minutes % 60 }}m
                                                @else
                                                    <span class="text-muted">Calculating...</span>
                                                @endif
                                            </td>
                                            <td>{{ $timeLog->notes ?: 'No notes' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Assign Technician Modal -->
@if(!$appointment->technician && $appointment->status === 'pending')
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Technician</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('appointments.update', $appointment) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p>Assign a technician to: <strong>{{ $appointment->customer_name }}</strong></p>
                        <p>Service: <strong>{{ $appointment->serviceType->name }}</strong></p>
                        <p>Date: <strong>{{ $appointment->scheduled_at->format('M j, Y g:i A') }}</strong></p>

                        <div class="mb-3">
                            <label for="technician_id" class="form-label">Select Technician</label>
                            <select class="form-select" name="technician_id" id="technician_id" required>
                                <option value="">Choose a technician...</option>
                                @foreach($technicians ?? [] as $technician)
                                    @if($technician->status === 'active' && $technician->serviceTypes->contains($appointment->serviceType->id))
                                        <option value="{{ $technician->id }}">
                                            {{ $technician->user->name }} - {{ $technician->phone }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign Technician</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
