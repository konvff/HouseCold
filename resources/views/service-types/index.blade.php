@extends('layouts.dashboard')

@section('title', 'Service Types')
@section('breadcrumb', 'Service Types')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Service Types
                    </h2>
                    <p class="text-muted mb-0">Manage the types of services you offer</p>
                </div>
                <a href="{{ route('service-types.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Service Type
                </a>
            </div>
        </div>
    </div>

    <!-- Service Types Table -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">All Service Types</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($serviceTypes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Service Name</th>
                                        <th>Description</th>
                                        <th>Hourly Rate</th>
                                        <th>Est. Duration</th>
                                        <th>Status</th>
                                        <th>Technicians</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceTypes as $serviceType)
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
                                            <td>
                                                <span class="badge bg-info">{{ $serviceType->technicians->count() }} techs</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('service-types.show', $serviceType) }}"
                                                       class="btn btn-outline-primary btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('service-types.edit', $serviceType) }}"
                                                       class="btn btn-outline-warning btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('service-types.destroy', $serviceType) }}"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this service type?')">
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
                            <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No service types found</h5>
                            <p class="text-muted">Create your first service type to get started!</p>
                            <a href="{{ route('service-types.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Service Type
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
