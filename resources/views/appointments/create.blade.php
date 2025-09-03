<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - HouseCall Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Cardknox integration script -->
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-bottom: 3px solid #e9ecef;
            color: #6c757d;
        }
        .step.active {
            border-bottom-color: #007bff;
            color: #007bff;
            font-weight: bold;
        }
        .step.completed {
            border-bottom-color: #28a745;
            color: #28a745;
        }
        .form-section {
            display: none;
        }
        .form-section.active {
            display: block;
        }
        .time-slot {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .time-slot:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .time-slot.selected {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
        .payment-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="form-container">
            <div class="text-center mb-4">
                <h1><i class="fas fa-calendar-plus text-primary"></i> Book Your Appointment</h1>
                <p class="lead">Schedule a professional service at your convenience</p>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step-1">1. Service & Time</div>
                <div class="step" id="step-2">2. Details</div>
                <div class="step" id="step-3">3. Payment</div>
                <div class="step" id="step-4">4. Confirmation</div>
            </div>

            <form id="appointment-form" method="POST" action="{{ route('appointments.store') }}">
                @csrf

                <!-- Step 1: Service & Time Selection -->
                <div class="form-section active" id="section-1">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-tools me-2"></i>Select Service & Time</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="service_type_id" class="form-label">Service Type *</label>
                                <select class="form-select" id="service_type_id" name="service_type_id" required>
                                    <option value="">Choose a service...</option>
                                    @foreach($serviceTypes as $service)
                                        <option value="{{ $service->id }}" data-rate="{{ $service->hourly_rate }}" data-duration="{{ $service->estimated_duration_minutes }}">
                                            {{ $service->name }} - ${{ $service->hourly_rate }}/hour ({{ $service->estimated_duration_minutes }} min)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="appointment_date" class="form-label">Preferred Date *</label>
                                <input type="text" class="form-control" id="appointment_date" name="appointment_date" required readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Available Time Slots</label>
                                <div id="time-slots" class="row">
                                    <div class="col-12 text-center">
                                        <p class="text-muted">Select a service and date to see available time slots</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('home') }}'">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep()" id="next-btn-1" disabled>
                                    Next<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Customer Details -->
                <div class="form-section" id="section-2">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-user me-2"></i>Your Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="customer_address" class="form-label">Service Address *</label>
                                <textarea class="form-control" id="customer_address" name="customer_address" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="service_notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="service_notes" name="service_notes" rows="3" placeholder="Any specific requirements or details about your service..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="prevStep()">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep()">
                                    Next<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Payment -->
                <div class="form-section" id="section-3">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6>Service Summary</h6>
                                    <p><strong>Service:</strong> <span id="summary-service">-</span></p>
                                    <p><strong>Date & Time:</strong> <span id="summary-datetime">-</span></p>
                                    <p><strong>Estimated Cost:</strong> <span id="summary-cost">-</span></p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Payment Method</h6>
                                    <p class="text-muted">Your card will be authorized but not charged until the service is completed.</p>
                                </div>
                            </div>

                            <div class="payment-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="card_number" class="form-label">Card Number *</label>
                                        <input type="text" class="form-control" id="card_number" name="card_data[card_number]" required maxlength="19" placeholder="1234 5678 9012 3456">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="card_type" class="form-label">Card Type</label>
                                        <input type="text" class="form-control" id="card_type" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="expiry_month" class="form-label">Expiry Month *</label>
                                        <select class="form-select" id="expiry_month" name="card_data[expiry_month]" required>
                                            <option value="">MM</option>
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="expiry_year" class="form-label">Expiry Year *</label>
                                        <select class="form-select" id="expiry_year" name="card_data[expiry_year]" required>
                                            <option value="">YYYY</option>
                                            @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="cvv" class="form-label">CVV *</label>
                                        <input type="text" class="form-control" id="cvv" name="card_data[cvv]" required maxlength="4" placeholder="123">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" class="form-control" id="first_name" name="customer_info[first_name]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control" id="last_name" name="customer_info[last_name]" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="customer_info[email]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone *</label>
                                        <input type="tel" class="form-control" id="phone" name="customer_info[phone]" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Billing Address *</label>
                                    <input type="text" class="form-control" id="address" name="customer_info[address]" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="city" class="form-label">City *</label>
                                        <input type="text" class="form-control" id="city" name="customer_info[city]" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="state" class="form-label">State *</label>
                                        <input type="text" class="form-control" id="state" name="customer_info[state]" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="zip" class="form-label">ZIP Code *</label>
                                        <input type="text" class="form-control" id="zip" name="customer_info[zip]" required>
                                    </div>
                                </div>
                                <div id="card-errors" class="text-danger mb-3" role="alert"></div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="prevStep()">
                                    <i class="fas fa-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-primary" onclick="processPayment()" id="pay-btn">
                                    <i class="fas fa-lock me-2"></i>Authorize Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Confirmation -->
                <div class="form-section" id="section-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-check-circle me-2"></i>Appointment Confirmed</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Thank You!</h4>
                            <p class="lead">Your appointment has been scheduled successfully.</p>
                            <p>We'll notify you when a technician accepts your appointment.</p>
                            <div class="mt-4">
                                <a href="{{ route('home') }}" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>Return Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let currentStep = 1;
        let selectedTimeSlot = null;

        // Initialize date picker
        flatpickr("#appointment_date", {
            minDate: "today",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr) {
                if (document.getElementById('service_type_id').value) {
                    loadAvailableSlots();
                }
            }
        });

        // Service type change handler
        document.getElementById('service_type_id').addEventListener('change', function() {
            if (this.value && document.getElementById('appointment_date').value) {
                loadAvailableSlots();
            }
        });

        // Card number change handler for card type detection
        document.getElementById('card_number').addEventListener('input', function() {
            const cardType = detectCardType(this.value);
            document.getElementById('card_type').value = cardType.charAt(0).toUpperCase() + cardType.slice(1);
        });

        // Card type detection function
        function detectCardType(cardNumber) {
            const cleanNumber = cardNumber.replace(/\D/g, '');

            if (/^4/.test(cleanNumber)) {
                return 'visa';
            } else if (/^5[1-5]/.test(cleanNumber)) {
                return 'mastercard';
            } else if (/^3[47]/.test(cleanNumber)) {
                return 'amex';
            } else if (/^6/.test(cleanNumber)) {
                return 'discover';
            } else {
                return 'unknown';
            }
        }

        // Load available time slots
        function loadAvailableSlots() {
            const serviceId = document.getElementById('service_type_id').value;
            const date = document.getElementById('appointment_date').value;

            if (!serviceId || !date) return;

            fetch(`/available-slots?service_type_id=${serviceId}&date=${date}`)
                .then(response => response.json())
                .then(data => {
                    displayTimeSlots(data);
                })
                .catch(error => {
                    console.error('Error loading time slots:', error);
                });
        }

        // Display time slots
        function displayTimeSlots(data) {
            const container = document.getElementById('time-slots');
            const slots = data.time_slots || [];

            if (slots.length === 0) {
                container.innerHTML = `
                    <div class="col-12 text-center">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Available Time Slots</h6>
                        <p class="text-muted">No technicians are available for ${data.service_type} on ${new Date(data.date).toLocaleDateString()}</p>
                        <p class="text-muted small">Try selecting a different date or service type.</p>
                    </div>`;
                return;
            }

            container.innerHTML = `
                <div class="col-12 mb-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>${data.service_type}</strong> - Estimated duration: <strong>${data.service_duration} minutes</strong>
                    </div>
                </div>
                ${slots.map(slot => `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="time-slot" data-datetime="${slot.datetime}" onclick="selectTimeSlot(this, '${slot.datetime}')">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <strong>${slot.start_time} - ${slot.end_time}</strong>
                                </div>
                                <span class="badge bg-success">Available</span>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-user-tie me-1"></i>${slot.technician_name}
                            </small>
                        </div>
                    </div>
                `).join('')}
            `;
        }

        // Select time slot
        function selectTimeSlot(element, datetime) {
            // Remove previous selection
            document.querySelectorAll('.time-slot').forEach(slot => slot.classList.remove('selected'));
            element.classList.add('selected');

            selectedTimeSlot = datetime;
            document.getElementById('next-btn-1').disabled = false;

            // Update the hidden input for the selected datetime
            if (!document.getElementById('selected_datetime')) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = 'selected_datetime';
                hiddenInput.name = 'scheduled_at';
                document.getElementById('appointment-form').appendChild(hiddenInput);
            }
            document.getElementById('selected_datetime').value = datetime;
        }

        // Navigation functions
        function nextStep() {
            if (currentStep < 4) {
                if (validateCurrentStep()) {
                    document.getElementById(`section-${currentStep}`).classList.remove('active');
                    currentStep++;
                    document.getElementById(`section-${currentStep}`).classList.add('active');
                    updateStepIndicator();

                    if (currentStep === 3) {
                        updateSummary();
                        populateCustomerInfo();
                    }
                }
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                document.getElementById(`section-${currentStep}`).classList.remove('active');
                currentStep--;
                document.getElementById(`section-${currentStep}`).classList.add('active');
                updateStepIndicator();
            }
        }

        function updateStepIndicator() {
            for (let i = 1; i <= 4; i++) {
                const step = document.getElementById(`step-${i}`);
                if (i < currentStep) {
                    step.classList.add('completed');
                    step.classList.remove('active');
                } else if (i === currentStep) {
                    step.classList.add('active');
                    step.classList.remove('completed');
                } else {
                    step.classList.remove('active', 'completed');
                }
            }
        }

        function validateCurrentStep() {
            if (currentStep === 1) {
                return document.getElementById('service_type_id').value && selectedTimeSlot;
            } else if (currentStep === 2) {
                return document.getElementById('customer_name').value &&
                       document.getElementById('customer_phone').value &&
                       document.getElementById('customer_address').value;
            }
            return true;
        }

        function updateSummary() {
            const serviceSelect = document.getElementById('service_type_id');
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];

            document.getElementById('summary-service').textContent = selectedOption.text;
            document.getElementById('summary-datetime').textContent = `${document.getElementById('appointment_date').value} at ${new Date(selectedTimeSlot).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;

            const rate = parseFloat(selectedOption.dataset.rate);
            const duration = parseInt(selectedOption.dataset.duration);
            const cost = (rate / 60) * duration;
            document.getElementById('summary-cost').textContent = `$${cost.toFixed(2)}`;
        }

        function populateCustomerInfo() {
            // Auto-populate customer info from step 2
            const customerName = document.getElementById('customer_name').value;
            const customerPhone = document.getElementById('customer_phone').value;
            const customerAddress = document.getElementById('customer_address').value;

            if (customerName) {
                const nameParts = customerName.split(' ');
                document.getElementById('first_name').value = nameParts[0] || '';
                document.getElementById('last_name').value = nameParts.slice(1).join(' ') || '';
            }
            document.getElementById('phone').value = customerPhone;
            document.getElementById('address').value = customerAddress;
        }

        function processPayment() {
            const payBtn = document.getElementById('pay-btn');
            payBtn.disabled = true;
            payBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            // Validate all required fields
            const requiredFields = [
                'card_number', 'expiry_month', 'expiry_year', 'cvv',
                'first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip'
            ];

            let isValid = true;
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                payBtn.disabled = false;
                payBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Authorize Payment';
                document.getElementById('card-errors').textContent = 'Please fill in all required fields.';
                return;
            }

            // Clear any previous errors
            document.getElementById('card-errors').textContent = '';

            // Submit the form
            document.getElementById('appointment-form').submit();
        }
    </script>
</body>
</html>
