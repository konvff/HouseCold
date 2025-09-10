@extends('layouts.dashboard')

@section('title', $technician->user->name)
@section('breadcrumb', $technician->user->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>{{ $technician->user->name }}
                    </h2>
                    <p class="text-muted mb-0">Technician Profile</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('technicians.edit', $technician) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('technicians.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Technician Details -->
    <div class="row">
        <div class="col-md-8">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">Technician Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <p class="mb-0">{{ $technician->user->name }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div>
                                <span class="badge bg-{{ $technician->status === 'active' ? 'success' : ($technician->status === 'inactive' ? 'secondary' : 'warning') }}">
                                    {{ $technician->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <p class="mb-0">{{ $technician->user->email }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <p class="mb-0">{{ $technician->phone }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Hourly Rate</label>
                            <p class="mb-0">
                                @if($technician->hourly_rate)
                                    <span class="badge bg-success fs-6">${{ $technician->hourly_rate }}/hour</span>
                                @else
                                    <span class="text-muted">Not set (uses service type default)</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <p class="mb-0">
                                <span class="badge bg-info">{{ $technician->user->role->label() }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Specialties</label>
                        <p class="mb-0">
                            @if($technician->specialties && is_array($technician->specialties) && count($technician->specialties) > 0)
                                @foreach($technician->specialties as $specialty)
                                    <span class="badge bg-light text-dark me-1">{{ $specialty }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">No specialties defined</span>
                            @endif
                        </p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Created</label>
                            <p class="mb-0">{{ $technician->created_at->format('M j, Y g:i A') }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <p class="mb-0">{{ $technician->updated_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Statistics Card -->
            <div class="card dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                        <h4>{{ $technician->appointments->count() }}</h4>
                        <p class="text-muted mb-0">Total Appointments</p>
                    </div>

                    <div class="text-center mb-3">
                        <i class="fas fa-tools fa-2x text-info mb-2"></i>
                        <h4>{{ $technician->serviceTypes->count() }}</h4>
                        <p class="text-muted mb-0">Service Types</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('technicians.edit', $technician) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Technician
                        </a>

                        <form method="POST" action="{{ route('technicians.destroy', $technician) }}"
                              onsubmit="return confirm('Are you sure you want to delete this technician? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>Delete Technician
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Types -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Assigned Service Types
                    </h5>
                </div>
                <div class="card-body">
                    @if($technician->serviceTypes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Service Name</th>
                                        <th>Description</th>
                                        <th>Hourly Rate</th>
                                        <th>Est. Duration</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($technician->serviceTypes as $serviceType)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-tools text-primary me-2"></i>
                                                    <strong>{{ $serviceType->name }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ Str::limit($serviceType->description, 50) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">${{ $serviceType->hourly_rate }}/hr</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $serviceType->estimated_duration_minutes }} min</span>
                                            </td>
                                            <td>
                                                @if($serviceType->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No service types assigned</h5>
                            <p class="text-muted">This technician doesn't have any service types assigned yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Recent Appointments
                    </h5>
                </div>
                <div class="card-body">
                    @if($technician->appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                        <th>Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($technician->appointments->take(10) as $appointment)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $appointment->customer_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $appointment->customer_email }}</small>
                                                </div>
                                            </td>
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
                                                <span class="status-badge status-{{ $appointment->status }}">
                                                    {{ $appointment->status->label() }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($appointment->actual_cost)
                                                    <strong class="text-success">${{ $appointment->actual_cost }}</strong>
                                                @else
                                                    <span class="text-muted">Est: ${{ $appointment->estimated_cost }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No appointments yet</h5>
                            <p class="text-muted">This technician hasn't been assigned to any appointments yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
