@extends('layouts.dashboard')

@section('title', 'Edit Appointment')
@section('breadcrumb', 'Edit Appointment')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Appointment
                    </h2>
                    <p class="text-muted mb-0">Modify appointment information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-eye me-2"></i>View Details
                    </a>
                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Appointment Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('appointments.update', $appointment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Customer Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Customer Name *</label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                       id="customer_name" name="customer_name"
                                       value="{{ $appointment->customer_name ?? '' }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="customer_phone" class="form-label">Customer Phone *</label>
                                <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror"
                                       id="customer_phone" name="customer_phone"
                                       value="{{ $appointment->customer_phone ?? '' }}" required>
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_email" class="form-label">Customer Email</label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                       id="customer_email" name="customer_email"
                                       value="{{ $appointment->customer_email ?? '' }}">
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="customer_address" class="form-label">Customer Address *</label>
                            <input type="text" class="form-control @error('customer_address') is-invalid @enderror"
                                   id="customer_address" name="customer_address"
                                   placeholder="Start typing your address..." required
                                   value="{{ $appointment->customer_address ?? '' }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Type your address and select from the suggestions, or click on the map to set your location.
                            </small>
                            <div id="map" style="height: 300px; width: 100%; margin-top: 10px; border-radius: 8px; border: 1px solid #e9ecef;"></div>
                            <input type="hidden" id="address_lat" name="address_lat" value="{{ $appointment->address_lat ?? '' }}">
                            <input type="hidden" id="address_lng" name="address_lng" value="{{ $appointment->address_lng ?? '' }}">
                            <input type="hidden" id="address_components" name="address_components" value="{{ $appointment->address_components ? json_encode($appointment->address_components) : '' }}">
                            @error('customer_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Service Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="service_type_id" class="form-label">Service Type *</label>
                                <select class="form-select @error('service_type_id') is-invalid @enderror"
                                        id="service_type_id" name="service_type_id" required>
                                    <option value="">Choose a service...</option>
                                    @foreach($serviceTypes as $service)
                                        <option value="{{ $service->id }}"
                                                {{ old('service_type_id', $appointment->service_type_id) == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} - ${{ $service->hourly_rate }}/hour
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="scheduled_at" class="form-label">Scheduled Date & Time *</label>
                                <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror"
                                       id="scheduled_at" name="scheduled_at"
                                       value="{{ $appointment->scheduled_at ? $appointment->scheduled_at->format('Y-m-d\TH:i') : '' }}" required>
                                @error('scheduled_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Technician Assignment -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="technician_id" class="form-label">Assign Technician</label>
                                <select class="form-select @error('technician_id') is-invalid @enderror"
                                        id="technician_id" name="technician_id">
                                    <option value="">No technician assigned</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}"
                                                {{ old('technician_id', $appointment->technician_id) == $technician->id ? 'selected' : '' }}>
                                            {{ $technician->user->name }} - {{ is_array($technician->specialties) ? implode(', ', $technician->specialties) : $technician->specialties }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('technician_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="pending" {{ old('status', $appointment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="in_progress" {{ old('status', $appointment->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Service Notes -->
                        <div class="mb-3">
                            <label for="service_notes" class="form-label">Service Notes</label>
                            <textarea class="form-control @error('service_notes') is-invalid @enderror"
                                      id="service_notes" name="service_notes" rows="3">{{ $appointment->service_notes ?? '' }}</textarea>
                            @error('service_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Appointment
                            </button>
                            <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-md-4">
            <!-- Current Appointment Info -->
            <div class="card dashboard-card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Current Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Appointment ID</small>
                        <p class="mb-0 fw-bold">#{{ $appointment->id }}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Created</small>
                        <p class="mb-0">{{ $appointment->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Estimated Cost</small>
                        <p class="mb-0 fw-bold text-success">${{ number_format($appointment->estimated_cost, 2) }}</p>
                    </div>
                    @if($appointment->actual_cost)
                    <div class="mb-2">
                        <small class="text-muted">Actual Cost</small>
                        <p class="mb-0 fw-bold text-info">${{ number_format($appointment->actual_cost, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Status -->
            @if($appointment->paymentHold)
            <div class="card dashboard-card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Payment Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Status</small>
                        <div>
                            <span class="badge bg-{{ $appointment->paymentHold->status == 'authorized' ? 'warning' : ($appointment->paymentHold->status == 'captured' ? 'success' : 'secondary') }}">
                                {{ $appointment->paymentHold->status->label() }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Amount</small>
                        <p class="mb-0 fw-bold">${{ number_format($appointment->paymentHold->amount, 2) }}</p>
                    </div>
                    @if($appointment->paymentHold->captured_at && $appointment->paymentHold->captured_at instanceof \Carbon\Carbon)
                    <div class="mb-2">
                        <small class="text-muted">Captured</small>
                        <p class="mb-0">{{ $appointment->paymentHold->captured_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card dashboard-card">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($appointment->status === 'completed' && $appointment->paymentHold && $appointment->paymentHold->status === 'authorized')
                        <button type="button" class="btn btn-success btn-sm" onclick="capturePayment()">
                            <i class="fas fa-credit-card me-2"></i>Capture Payment
                        </button>
                        @elseif($appointment->status === 'completed' && $appointment->paymentHold && $appointment->paymentHold->status === 'captured')
                        <div class="alert alert-success small mb-0">
                            <i class="fas fa-check-circle me-2"></i>Payment already captured: ${{ number_format($appointment->paymentHold->amount, 2) }}
                        </div>
                        @endif
                        @if($appointment->status === 'pending')
                        <button type="button" class="btn btn-info btn-sm" onclick="assignTechnician()">
                            <i class="fas fa-user-plus me-2"></i>Assign Technician
                        </button>
                        @endif
                        @if($appointment->status !== 'cancelled')
                        <button type="button" class="btn btn-danger btn-sm" onclick="cancelAppointment()">
                            <i class="fas fa-times me-2"></i>Cancel Appointment
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Capture Payment Modal -->
<div class="modal fade" id="capturePaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Capture Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to capture the payment for this appointment?</p>
                <p class="text-muted">
                    @if($appointment->actual_cost)
                        Actual Cost: ${{ number_format($appointment->actual_cost, 2) }}
                    @else
                        Estimated Cost: ${{ number_format($appointment->estimated_cost, 2) }}
                    @endif
                </p>
                <p class="text-info small">The system will charge the actual cost based on time worked, capped at the authorized amount.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('appointments.capture-payment', $appointment) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">Capture Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Assign Technician Modal -->
<div class="modal fade" id="assignTechnicianModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignTechnicianForm" action="{{ route('appointments.assign-technician', $appointment) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="modal_technician_id" class="form-label">Select Technician</label>
                        <select class="form-select" id="modal_technician_id" name="technician_id" required>
                            <option value="">Choose a technician...</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}">
                                    {{ $technician->user->name }} - {{ is_array($technician->specialties) ? implode(', ', $technician->specialties) : $technician->specialties }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="assignTechnicianForm" class="btn btn-primary">Assign Technician</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Appointment Modal -->
<div class="modal fade" id="cancelAppointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this appointment?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Appointment</button>
                <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function capturePayment() {
    $('#capturePaymentModal').modal('show');
}

function assignTechnician() {
    $('#assignTechnicianModal').modal('show');
}

function cancelAppointment() {
    $('#cancelAppointmentModal').modal('show');
}

// Auto-update technician in main form when assigned via modal
document.getElementById('modal_technician_id').addEventListener('change', function() {
    document.getElementById('technician_id').value = this.value;
});

// Google Maps Integration
let map;
let marker;
let autocomplete;
let geocoder;

function initGoogleMaps() {
    const apiKey = '{{ config("services.google_maps.api_key") }}';

    if (!apiKey || apiKey === 'your_google_maps_api_key_here') {
        // Fallback: Hide map and show manual input
        document.getElementById('map').style.display = 'none';
        document.querySelector('small.form-text').innerHTML =
            '<i class="fas fa-info-circle me-1"></i>Please enter your full address manually.';
        return;
    }

    // Load Google Maps API with marker library
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places,marker&callback=initMap`;
    script.async = true;
    script.defer = true;
    script.onerror = function() {
        console.error('Failed to load Google Maps API');
        document.getElementById('map').style.display = 'none';
        document.querySelector('small.form-text').innerHTML =
            '<i class="fas fa-exclamation-triangle me-1"></i>Google Maps failed to load. Please enter your address manually.';
    };
    document.head.appendChild(script);
}

function initMap() {
    // Initialize map
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 40.7128, lng: -74.0060 }, // Default to NYC
        zoom: 13,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false
    });

    // Initialize geocoder
    geocoder = new google.maps.Geocoder();

    // Initialize autocomplete
    const addressInput = document.getElementById('customer_address');
    autocomplete = new google.maps.places.Autocomplete(addressInput, {
        types: ['address'],
        componentRestrictions: { country: 'us' } // Restrict to US addresses
    });

    // Handle autocomplete selection
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        if (place.geometry) {
            const location = place.geometry.location;
            map.setCenter(location);
            map.setZoom(16);

            // Update marker using AdvancedMarkerElement
            if (marker) {
                marker.map = null;
            }
            marker = new google.maps.marker.AdvancedMarkerElement({
                position: location,
                map: map,
                title: place.formatted_address
            });

            // Update hidden fields
            document.getElementById('address_lat').value = location.lat();
            document.getElementById('address_lng').value = location.lng();
            document.getElementById('address_components').value = JSON.stringify(place.address_components);
        }
    });

    // Handle map clicks
    map.addListener('click', function(event) {
        const location = event.latLng;

        // Update marker using AdvancedMarkerElement
        if (marker) {
            marker.map = null;
        }
        marker = new google.maps.marker.AdvancedMarkerElement({
            position: location,
            map: map
        });

        // Reverse geocode to get address
        geocoder.geocode({ location: location }, function(results, status) {
            if (status === 'OK' && results[0]) {
                const address = results[0].formatted_address;
                document.getElementById('customer_address').value = address;
                document.getElementById('address_lat').value = location.lat();
                document.getElementById('address_lng').value = location.lng();
                document.getElementById('address_components').value = JSON.stringify(results[0].address_components);
            }
        });
    });

    // Try to geocode existing address if any
    const existingAddress = addressInput.value;
    const existingLat = document.getElementById('address_lat').value;
    const existingLng = document.getElementById('address_lng').value;

    if (existingLat && existingLng) {
        const location = new google.maps.LatLng(parseFloat(existingLat), parseFloat(existingLng));
        map.setCenter(location);
        map.setZoom(16);

        marker = new google.maps.marker.AdvancedMarkerElement({
            position: location,
            map: map,
            title: existingAddress
        });
    } else if (existingAddress) {
        geocoder.geocode({ address: existingAddress }, function(results, status) {
            if (status === 'OK' && results[0]) {
                const location = results[0].geometry.location;
                map.setCenter(location);
                map.setZoom(16);

                marker = new google.maps.marker.AdvancedMarkerElement({
                    position: location,
                    map: map,
                    title: existingAddress
                });
            }
        });
    }
}

// Initialize Google Maps when page loads
document.addEventListener('DOMContentLoaded', function() {
    initGoogleMaps();
});
</script>
@endpush
