@extends('layouts.dashboard')

@section('title', 'Edit Technician')
@section('breadcrumb', 'Edit Technician')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Technician
                    </h2>
                    <p class="text-muted mb-0">Modify technician: {{ $technician->user->name }}</p>
                </div>
                <a href="{{ route('technicians.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">Technician Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('technicians.update', $technician) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $technician->user->name) }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $technician->user->email) }}"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone', $technician->phone) }}"
                                       required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="hourly_rate" class="form-label">Hourly Rate ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                           class="form-control @error('hourly_rate') is-invalid @enderror"
                                           id="hourly_rate"
                                           name="hourly_rate"
                                           value="{{ old('hourly_rate', $technician->hourly_rate) }}"
                                           step="0.01"
                                           min="0">
                                </div>
                                @error('hourly_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Leave blank to use service type default rate</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status"
                                        name="status">
                                    <option value="active" {{ old('status', $technician->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $technician->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="on_leave" {{ old('status', $technician->status) === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="Leave blank to keep current password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Only fill if you want to change the password</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="specialties" class="form-label">Specialties</label>
                            <textarea class="form-control @error('specialties') is-invalid @enderror"
                                      id="specialties"
                                      name="specialties"
                                      rows="3"
                                      placeholder="Enter specialties separated by commas (e.g., HVAC, Electrical, Plumbing)">{{ old('specialties', is_array($technician->specialties) ? implode(', ', $technician->specialties) : $technician->specialties) }}</textarea>
                            @error('specialties')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Enter specialties separated by commas</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Service Types</label>
                            <div class="row">
                                @foreach($serviceTypes as $serviceType)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   name="service_types[]"
                                                   value="{{ $serviceType->id }}"
                                                   id="service_{{ $serviceType->id }}"
                                                   {{ in_array($serviceType->id, old('service_types', $technician->serviceTypes->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="service_{{ $serviceType->id }}">
                                                {{ $serviceType->name }}
                                                <br>
                                                <small class="text-muted">${{ $serviceType->hourly_rate }}/hr</small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('service_types')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('technicians.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Technician
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
