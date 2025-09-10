@extends('layouts.dashboard')

@section('title', 'Technicians')
@section('breadcrumb', 'Technicians')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-users-cog me-2"></i>Technicians
                    </h2>
                    <p class="text-muted mb-0">Manage your team of technicians</p>
                </div>
                <a href="{{ route('technicians.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Technician
                </a>
            </div>
        </div>
    </div>

    <!-- Technicians Table -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">All Technicians</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($technicians->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Technician</th>
                                        <th>Contact Info</th>
                                        <th>Specialties</th>
                                        <th>Hourly Rate</th>
                                        <th>Status</th>
                                        <th>Service Types</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($technicians as $technician)
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
                                            <td>
                                                <div>
                                                    <i class="fas fa-phone text-muted me-1"></i>{{ $technician->phone }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($technician->specialties && is_array($technician->specialties) && count($technician->specialties) > 0)
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
                                            <td>
                                                <span class="badge bg-info">{{ $technician->serviceTypes->count() }} services</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('technicians.show', $technician) }}"
                                                       class="btn btn-outline-primary btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('technicians.edit', $technician) }}"
                                                       class="btn btn-outline-warning btn-sm"
                                                       data-bs-toggle="tooltip"
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('technicians.destroy', $technician) }}"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this technician?')">
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
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No technicians found</h5>
                            <p class="text-muted">Add your first technician to start building your team!</p>
                            <a href="{{ route('technicians.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Technician
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
