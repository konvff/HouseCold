@extends('layouts.dashboard')

@section('title', $serviceType->name)
@section('breadcrumb', $serviceType->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-tools me-2"></i>{{ $serviceType->name }}
                    </h2>
                    <p class="text-muted mb-0">Service Type Details</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('service-types.edit', $serviceType) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('service-types.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Type Details -->
    <div class="row">
        <div class="col-md-8">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">Service Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Service Name</label>
                            <p class="mb-0">{{ $serviceType->name }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div>
                                @if($serviceType->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Hourly Rate</label>
                            <p class="mb-0">
                                <span class="badge bg-success fs-6">${{ $serviceType->hourly_rate }}/hour</span>
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estimated Duration</label>
                            <p class="mb-0">{{ $serviceType->estimated_duration_minutes }} minutes</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <p class="mb-0">{{ $serviceType->description ?: 'No description provided.' }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Created</label>
                            <p class="mb-0">{{ $serviceType->created_at->format('M j, Y g:i A') }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <p class="mb-0">{{ $serviceType->updated_at->format('M j, Y g:i A') }}</p>
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
                        <h4>{{ $serviceType->appointments->count() }}</h4>
                        <p class="text-muted mb-0">Total Appointments</p>
                    </div>

                    <div class="text-center mb-3">
                        <i class="fas fa-users fa-2x text-info mb-2"></i>
                        <h4>{{ $serviceType->technicians->count() }}</h4>
                        <p class="text-muted mb-0">Assigned Technicians</p>
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
                        <a href="{{ route('service-types.edit', $serviceType) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Service Type
                        </a>

                        <form method="POST" action="{{ route('service-types.destroy', $serviceType) }}"
                              onsubmit="return confirm('Are you sure you want to delete this service type? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>Delete Service Type
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Technicians -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Assigned Technicians
                    </h5>
                </div>
                <div class="card-body">
                    @if($serviceType->technicians->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Technician</th>
                                        <th>Phone</th>
                                        <th>Specialties</th>
                                        <th>Hourly Rate</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceType->technicians as $technician)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-tie text-info me-2"></i>
                                                    <div>
                                                        <strong>{{ $technician->user->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $technician->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $technician->phone }}</td>
                                            <td>
                                                @if($technician->specialties)
                                                    @foreach($technician->specialties as $specialty)
                                                        <span class="badge bg-light text-dark me-1">{{ $specialty }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No specialties</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($technician->hourly_rate)
                                                    <span class="badge bg-success">${{ $technician->hourly_rate }}/hr</span>
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $technician->status === 'active' ? 'success' : ($technician->status === 'inactive' ? 'secondary' : 'warning') }}">
                                                    {{ $technician->status->label() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No technicians assigned</h5>
                            <p class="text-muted">This service type doesn't have any technicians assigned yet.</p>
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
                    @if($serviceType->appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Date & Time</th>
                                        <th>Technician</th>
                                        <th>Status</th>
                                        <th>Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceType->appointments->take(10) as $appointment)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $appointment->customer_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $appointment->customer_email }}</small>
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
                                                    <span class="text-info">{{ $appointment->technician->user->name }}</span>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
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
                            <p class="text-muted">This service type hasn't been booked for any appointments yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
