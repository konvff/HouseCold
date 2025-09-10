@extends('layouts.dashboard')

@section('title', 'Edit Time Slot')
@section('breadcrumb', 'Edit Time Slot')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Availability Time Slot
                    </h2>
                    <p class="text-muted mb-0">Modify technician availability settings</p>
                </div>
                <a href="{{ route('technician-availabilities.index') }}" class="btn btn-outline-secondary">
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
                    <h5 class="mb-0">Edit Time Slot Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('technician-availabilities.update', $availability) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="technician_id" class="form-label">Technician <span class="text-danger">*</span></label>
                                <select class="form-select @error('technician_id') is-invalid @enderror"
                                        id="technician_id"
                                        name="technician_id"
                                        required>
                                    <option value="">Choose a technician...</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}" {{ old('technician_id', $availability->technician_id) == $technician->id ? 'selected' : '' }}>
                                            {{ $technician->user->name }} - {{ $technician->user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('technician_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="day_of_week" class="form-label">Day of Week <span class="text-danger">*</span></label>
                                <select class="form-select @error('day_of_week') is-invalid @enderror"
                                        id="day_of_week"
                                        name="day_of_week"
                                        required>
                                    <option value="">Choose a day...</option>
                                    @foreach($daysOfWeek as $day)
                                        <option value="{{ $day }}" {{ old('day_of_week', $availability->day_of_week) === $day ? 'selected' : '' }}>
                                            {{ ucfirst($day) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('day_of_week')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="time"
                                       class="form-control @error('start_time') is-invalid @enderror"
                                       id="start_time"
                                       name="start_time"
                                       value="{{ old('start_time', $availability->start_time->format('H:i')) }}"
                                       required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">When the technician starts being available</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time"
                                       class="form-control @error('end_time') is-invalid @enderror"
                                       id="end_time"
                                       name="end_time"
                                       value="{{ old('end_time', $availability->end_time->format('H:i')) }}"
                                       required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">When the technician stops being available</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date"
                                       class="form-control @error('start_date') is-invalid @enderror"
                                       id="start_date"
                                       name="start_date"
                                       value="{{ old('start_date', $availability->start_date ? $availability->start_date->format('Y-m-d') : '') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Leave blank for indefinite availability</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date"
                                       class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date"
                                       name="end_date"
                                       value="{{ old('end_date', $availability->end_date ? $availability->end_date->format('Y-m-d') : '') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Leave blank for indefinite availability</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="is_recurring" class="form-label">Recurring</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="is_recurring"
                                           name="is_recurring"
                                           value="1"
                                           {{ old('is_recurring', $availability->is_recurring) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_recurring">
                                        This time slot repeats weekly
                                    </label>
                                </div>
                                <small class="form-text text-muted">Uncheck for one-time availability</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="is_active" class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="is_active"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', $availability->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Time slot is active and available for booking
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('technician-availabilities.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Time Slot
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Current Time Slot Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Technician:</strong> {{ $availability->technician->user->name }}</p>
                            <p><strong>Day:</strong> {{ $availability->day_of_week->label() }}</p>
                            <p><strong>Time:</strong> {{ $availability->start_time->format('g:i A') }} - {{ $availability->end_time->format('g:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Type:</strong>
                                @if($availability->is_recurring)
                                    <span class="badge bg-success">Recurring</span>
                                @else
                                    <span class="badge bg-warning">One-time</span>
                                @endif
                            </p>
                            <p><strong>Status:</strong>
                                @if($availability->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                            <p><strong>Created:</strong> {{ $availability->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-fill end date when start date is selected (for one-time slots)
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = this.value;
    const endDateField = document.getElementById('end_date');
    const recurringCheckbox = document.getElementById('is_recurring');

    if (startDate && !recurringCheckbox.checked) {
        // For one-time slots, suggest end date as 1 week later
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + 7);
        endDateField.value = endDate.toISOString().split('T')[0];
    }
});

// Handle recurring checkbox change
document.getElementById('is_recurring').addEventListener('change', function() {
    const startDateField = document.getElementById('start_date');
    const endDateField = document.getElementById('end_date');

    if (this.checked) {
        // For recurring slots, clear end date
        endDateField.value = '';
        endDateField.placeholder = 'Leave blank for ongoing availability';
    } else {
        // For one-time slots, require end date
        endDateField.placeholder = 'Required for one-time availability';
    }
});
</script>
@endsection
