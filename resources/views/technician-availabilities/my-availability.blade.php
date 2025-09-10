@extends('layouts.dashboard')

@section('title', 'My Availability')
@section('breadcrumb', 'My Availability')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-clock me-2"></i>My Availability
                    </h2>
                    <p class="text-muted mb-0">Manage when you're available for appointments</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                    <i class="fas fa-plus me-2"></i>Add Time Slot
                </button>
            </div>
        </div>
    </div>

    <!-- My Availability Table -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">My Available Time Slots</h5>
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
                                                    <button type="button"
                                                            class="btn btn-outline-warning btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editAvailabilityModal{{ $availability->id }}"
                                                            title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('technician-availabilities.destroy-my', $availability) }}"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to remove this time slot?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm"
                                                                title="Remove">
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
                            <p class="text-muted">Add time slots to define when you're available for appointments.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAvailabilityModal">
                                <i class="fas fa-plus me-2"></i>Add Time Slot
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                    <h4>{{ $availabilities->where('is_active', true)->count() }}</h4>
                    <p class="text-muted mb-0">Active Time Slots</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-info mb-2"></i>
                    <h4>{{ $availabilities->where('is_recurring', true)->count() }}</h4>
                    <p class="text-muted mb-0">Recurring Slots</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
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

<!-- Add Availability Modal -->
<div class="modal fade" id="addAvailabilityModal" tabindex="-1" aria-labelledby="addAvailabilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAvailabilityModalLabel">Add Availability Time Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('technician-availabilities.store-my') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="day_of_week" class="form-label">Day of Week <span class="text-danger">*</span></label>
                            <select class="form-select @error('day_of_week') is-invalid @enderror"
                                    id="day_of_week"
                                    name="day_of_week"
                                    required>
                                <option value="">Choose a day...</option>
                                @foreach($daysOfWeek as $day)
                                    <option value="{{ $day }}" {{ old('day_of_week') === $day ? 'selected' : '' }}>
                                        {{ ucfirst($day) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('day_of_week')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time"
                                   class="form-control @error('start_time') is-invalid @enderror"
                                   id="start_time"
                                   name="start_time"
                                   value="{{ old('start_time') }}"
                                   required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time"
                                   class="form-control @error('end_time') is-invalid @enderror"
                                   id="end_time"
                                   name="end_time"
                                   value="{{ old('end_time') }}"
                                   required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date"
                                   class="form-control @error('start_date') is-invalid @enderror"
                                   id="start_date"
                                   name="start_date"
                                   value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date"
                                   class="form-control @error('end_date') is-invalid @enderror"
                                   id="end_date"
                                   name="end_date"
                                   value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="is_recurring" class="form-label">Recurring</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_recurring"
                                       name="is_recurring"
                                       value="1"
                                       {{ old('is_recurring', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_recurring">
                                    This time slot repeats weekly
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Time slot is active and available for booking
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Time Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Availability Modals -->
@foreach($availabilities as $availability)
<div class="modal fade" id="editAvailabilityModal{{ $availability->id }}" tabindex="-1" aria-labelledby="editAvailabilityModalLabel{{ $availability->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAvailabilityModalLabel{{ $availability->id }}">Edit Availability Time Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('technician-availabilities.update-my', $availability) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_day_of_week_{{ $availability->id }}" class="form-label">Day of Week <span class="text-danger">*</span></label>
                            <select class="form-select"
                                    id="edit_day_of_week_{{ $availability->id }}"
                                    name="day_of_week"
                                    required>
                                @foreach($daysOfWeek as $day)
                                    <option value="{{ $day }}" {{ $availability->day_of_week === $day ? 'selected' : '' }}>
                                        {{ ucfirst($day) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_start_time_{{ $availability->id }}" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time"
                                   class="form-control"
                                   id="edit_start_time_{{ $availability->id }}"
                                   name="start_time"
                                   value="{{ $availability->start_time->format('H:i') }}"
                                   required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_end_time_{{ $availability->id }}" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time"
                                   class="form-control"
                                   id="edit_end_time_{{ $availability->id }}"
                                   name="end_time"
                                   value="{{ $availability->end_time->format('H:i') }}"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_start_date_{{ $availability->id }}" class="form-label">Start Date</label>
                            <input type="date"
                                   class="form-control"
                                   id="edit_start_date_{{ $availability->id }}"
                                   name="start_date"
                                   value="{{ $availability->start_date ? $availability->start_date->format('Y-m-d') : '' }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_end_date_{{ $availability->id }}" class="form-label">End Date</label>
                            <input type="date"
                                   class="form-control"
                                   id="edit_end_date_{{ $availability->id }}"
                                   name="end_date"
                                   value="{{ $availability->end_date ? $availability->end_date->format('Y-m-d') : '' }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_is_recurring_{{ $availability->id }}" class="form-label">Recurring</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="edit_is_recurring_{{ $availability->id }}"
                                       name="is_recurring"
                                       value="1"
                                       {{ $availability->is_recurring ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_is_recurring_{{ $availability->id }}">
                                    This time slot repeats weekly
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_is_active_{{ $availability->id }}" class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="edit_is_active_{{ $availability->id }}"
                                       name="is_active"
                                       value="1"
                                       {{ $availability->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_is_active_{{ $availability->id }}">
                                    Time slot is active and available for booking
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Time Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
