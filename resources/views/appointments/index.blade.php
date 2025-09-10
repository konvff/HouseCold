@extends('layouts.dashboard')

@section('title', 'Appointments')
@section('breadcrumb', 'Appointments')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Appointments
                    </h2>
                    <p class="text-muted mb-0">Manage all house call appointments</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>New Appointment
                    </a>
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4>{{ $appointments->where('status', 'pending')->count() }}</h4>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-2x text-info mb-2"></i>
                    <h4>{{ $appointments->where('status', 'confirmed')->count() }}</h4>
                    <p class="text-muted mb-0">Confirmed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-tools fa-2x text-primary mb-2"></i>
                    <h4>{{ $appointments->where('status', 'in_progress')->count() }}</h4>
                    <p class="text-muted mb-0">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4>{{ $appointments->where('status', 'completed')->count() }}</h4>
                    <p class="text-muted mb-0">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">All Appointments</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Date & Time</th>
                                        <th>Technician</th>
                                        <th>Status</th>
                                        <th>Cost</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
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
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('appointments.show', $appointment) }}"
                                                       class="btn btn-outline-primary btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('appointments.edit', $appointment) }}"
                                                       class="btn btn-outline-warning btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if(!$appointment->technician && $appointment->status === 'pending')
                                                        <button class="btn btn-outline-info btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#assignModal{{ $appointment->id }}"
                                                                title="Assign Technician">
                                                            <i class="fas fa-user-plus"></i>
                                                        </button>
                                                    @endif
                                                                                                        @if($appointment->status === 'completed' && $appointment->paymentHold && $appointment->paymentHold->status === 'authorized')
                                                        <form method="POST" action="{{ route('appointments.capture-payment', $appointment) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-success btn-sm"
                                                                    title="Capture Payment"
                                                                    onclick="return confirm('Capture payment for completed work?')">
                                                                <i class="fas fa-credit-card"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if(in_array($appointment->status, ['pending', 'confirmed']))
                                                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                    title="Cancel Appointment"
                                                                    onclick="return confirm('Cancel this appointment? This will release the payment hold.')">
                                                                <i class="fas fa-times"></i>
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
                            <h5 class="text-muted">No appointments found</h5>
                            <p class="text-muted">Create your first appointment to get started!</p>
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>New Appointment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Appointments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('appointments.index') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" name="status" id="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="service_type" class="form-label">Service Type</label>
                        <select class="form-select" name="service_type" id="service_type">
                            <option value="">All Services</option>
                            @foreach($serviceTypes ?? [] as $serviceType)
                                <option value="{{ $serviceType->id }}" {{ request('service_type') == $serviceType->id ? 'selected' : '' }}>
                                    {{ $serviceType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="mb-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Technician Modals -->
@foreach($appointments->where('status', 'pending') as $appointment)
    @if(!$appointment->technician)
        <div class="modal fade" id="assignModal{{ $appointment->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Technician</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('appointments.assign-technician', $appointment) }}">
                        @csrf
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
@endforeach
@endsection
