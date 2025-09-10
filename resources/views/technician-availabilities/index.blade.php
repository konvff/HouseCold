@extends('layouts.dashboard')

@section('title', 'Technician Availability')
@section('breadcrumb', 'Technician Availability')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Technician Availability
                    </h2>
                    <p class="text-muted mb-0">Manage when technicians are available for appointments</p>
                </div>
                <a href="{{ route('technician-availabilities.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Time Slot
                </a>
            </div>
        </div>
    </div>

    <!-- Availability Table -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">All Availability Time Slots</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($availabilities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Technician</th>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th>Type</th>
                                        <th>Date Range</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availabilities as $availability)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-tie text-info me-2"></i>
                                                    <div>
                                                        <strong>{{ $availability->technician->user->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $availability->technician->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $availability->day_of_week->label() }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ \Carbon\Carbon::parse($availability->start_time)->format('g:i A') }}</strong>
                                                    <br>
                                                    <small class="text-muted">to {{ \Carbon\Carbon::parse($availability->end_time)->format('g:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($availability->is_recurring)
                                                    <span class="badge bg-success">Recurring</span>
                                                @else
                                                    <span class="badge bg-warning">One-time</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($availability->start_date && $availability->end_date)
                                                    <div>
                                                        <strong>{{ \Carbon\Carbon::parse($availability->start_date)->format('M j, Y') }}</strong>
                                                        <br>
                                                        <small class="text-muted">to {{ \Carbon\Carbon::parse($availability->end_date)->format('M j, Y') }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Indefinite</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($availability->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('technician-availabilities.edit', $availability) }}"
                                                       class="btn btn-outline-warning btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('technician-availabilities.destroy', $availability) }}"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this time slot?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm"
                                                                data-bs-toggle="tooltip"
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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
                            <h5 class="text-muted">No availability time slots found</h5>
                            <p class="text-muted">Add time slots to define when technicians are available for appointments.</p>
                            <a href="{{ route('technician-availabilities.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Time Slot
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h4>{{ $technicians->count() }}</h4>
                    <p class="text-muted mb-0">Total Technicians</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                    <h4>{{ $availabilities->where('is_active', true)->count() }}</h4>
                    <p class="text-muted mb-0">Active Time Slots</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-info mb-2"></i>
                    <h4>{{ $availabilities->where('is_recurring', true)->count() }}</h4>
                    <p class="text-muted mb-0">Recurring Slots</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-day fa-2x text-warning mb-2"></i>
                    <h4>{{ $availabilities->groupBy('day_of_week')->count() }}</h4>
                    <p class="text-muted mb-0">Days Covered</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
