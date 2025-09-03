<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ServiceType;
use App\Models\Technician;
use App\Models\TechnicianAvailability;
use App\Models\PaymentHold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\CardknoxPaymentService;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['serviceType', 'technician'])
            ->orderBy('scheduled_at', 'asc')
            ->paginate(15);

        $serviceTypes = ServiceType::where('is_active', true)->get();
        $technicians = Technician::where('status', 'active')->get();

        return view('appointments.index', compact('appointments', 'serviceTypes', 'technicians'));
    }

    public function create()
    {
        $serviceTypes = ServiceType::where('is_active', true)->get();
        return view('appointments.create', compact('serviceTypes'));
    }

    public function store(Request $request)
    {
        \Log::info('Appointment creation started', $request->all());

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'required|string',
            'service_type_id' => 'required|string|exists:service_types,id',
            'scheduled_at' => 'required|date|after:now',
            'service_notes' => 'nullable|string',
            'card_data' => 'required|array',
            'card_data.card_number' => 'required|string',
            'card_data.expiry_month' => 'required|string',
            'card_data.expiry_year' => 'required|string',
            'card_data.cvv' => 'required|string',
            'customer_info' => 'required|array',
            'customer_info.first_name' => 'required|string',
            'customer_info.last_name' => 'required|string',
            'customer_info.email' => 'required|email',
            'customer_info.phone' => 'required|string',
            'customer_info.address' => 'required|string',
            'customer_info.city' => 'required|string',
            'customer_info.state' => 'required|string',
            'customer_info.zip' => 'required|string'
        ]);

        $serviceType = ServiceType::findOrFail($request->service_type_id);
        $estimatedCost = ($serviceType->hourly_rate / 60) * $serviceType->estimated_duration_minutes;

        \Log::info('Service type and cost calculated', [
            'service_type' => $serviceType->name,
            'estimated_cost' => $estimatedCost
        ]);

        try {
            DB::beginTransaction();

            // Create appointment
            $appointment = Appointment::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_info['email'] ?? null,
                'customer_address' => $request->customer_address,
                'service_type_id' => $request->service_type_id,
                'scheduled_at' => $request->scheduled_at,
                'service_notes' => $request->service_notes,
                'estimated_duration_minutes' => $serviceType->estimated_duration_minutes,
                'estimated_cost' => $estimatedCost,
                'status' => 'pending'
            ]);

            // Create payment authorization hold with Cardknox
            \Log::info('Starting Cardknox payment authorization');

            $cardknoxService = new CardknoxPaymentService();
            $authResult = $cardknoxService->authorizePayment(
                $estimatedCost,
                $request->card_data,
                $request->customer_info,
                'APPOINTMENT_' . $appointment->id
            );

            if (!$authResult['success']) {
                throw new \Exception('Payment authorization failed: ' . $authResult['error']);
            }

            \Log::info('Cardknox authorization successful', [
                'transaction_id' => $authResult['transaction_id'],
                'auth_code' => $authResult['auth_code']
            ]);

            \Log::info('Creating PaymentHold record');
            PaymentHold::create([
                'appointment_id' => $appointment->id,
                'amount' => $estimatedCost,
                'status' => 'authorized',
                'cardknox_transaction_id' => $authResult['transaction_id'],
                'cardknox_auth_code' => $authResult['auth_code'],
                'card_last_four' => substr($request->card_data['card_number'], -4),
                'card_type' => $this->detectCardType($request->card_data['card_number']),
                'expires_at' => Carbon::now()->addDays(7)
            ]);
            \Log::info('PaymentHold record created successfully');

            // Find available technicians and send SMS notifications
            \Log::info('Notifying available technicians');
            $this->notifyAvailableTechnicians($appointment);

            DB::commit();
            \Log::info('Appointment creation completed successfully');

            return redirect()->route('appointments.show', $appointment)
                ->with('success', 'Appointment booked successfully! We will notify you when a technician accepts.');

        } catch (\Exception $e) {
            \Log::error('Appointment creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to book appointment: ' . $e->getMessage()]);
        }
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['serviceType', 'technician', 'paymentHold', 'timeLogs']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $serviceTypes = ServiceType::where('is_active', true)->get();
        $technicians = Technician::where('status', 'active')->get();
        return view('appointments.edit', compact('appointment', 'serviceTypes', 'technicians'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'required|string',
            'service_type_id' => 'required|exists:service_types,id',
            'scheduled_at' => 'required|date',
            'technician_id' => 'nullable|exists:technicians,id',
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled'
        ]);

        $oldStatus = $appointment->status;
        $appointment->update($request->all());

        // Note: Payment auto-capture is handled by TechnicianController when timer is stopped
        // No need to duplicate the auto-capture logic here

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully!');
    }

    // Note: autoCapturePayment method has been removed to prevent duplicate charging
    // Payment capture is now handled exclusively by TechnicianController when timer is stopped

    private function sendPaymentConfirmation(Appointment $appointment, $amount)
    {
        // TODO: Implement customer notification
        // This could be an email or SMS confirmation
        \Log::info('Payment confirmation sent to customer', [
            'appointment_id' => $appointment->id,
            'customer' => $appointment->customer_name,
            'amount' => $amount
        ]);
    }

    public function assignTechnician(Request $request, Appointment $appointment)
    {
        $request->validate([
            'technician_id' => 'required|exists:technicians,id'
        ]);

        $appointment->update([
            'technician_id' => $request->technician_id,
            'status' => 'confirmed'
        ]);

        // Send SMS notification to the assigned technician
        if ($appointment->technician) {
            $this->sendSMSNotification($appointment->technician, $appointment);
        }

        return redirect()->route('appointments.index')
            ->with('success', 'Technician assigned successfully!');
    }

    public function capturePayment(Appointment $appointment)
    {
        try {
            // Check if appointment is completed and has authorized payment
            if ($appointment->status !== 'completed') {
                return back()->withErrors(['error' => 'Appointment must be completed before capturing payment.']);
            }

            $paymentHold = $appointment->paymentHold;
            if (!$paymentHold || $paymentHold->status !== 'authorized') {
                return back()->withErrors(['error' => 'No authorized payment found for this appointment.']);
            }

            // Prevent double capture
            if ($paymentHold->status === 'captured') {
                return back()->withErrors(['error' => 'Payment has already been captured for this appointment.']);
            }

            // Calculate actual cost based on time worked
            $actualCost = $this->calculateActualCost($appointment);

            // Capture the payment using Cardknox
            $cardknoxService = new CardknoxPaymentService();
            $captureResult = $cardknoxService->capturePayment(
                $paymentHold->cardknox_transaction_id,
                $actualCost,
                'APPOINTMENT_' . $appointment->id
            );

            if ($captureResult['success']) {
                // Update payment hold status
                $paymentHold->update([
                    'status' => 'captured',
                    'amount' => $actualCost,
                    'captured_at' => now()
                ]);

                // Update appointment with actual cost
                $appointment->update([
                    'actual_cost' => $actualCost
                ]);
            } else {
                return back()->withErrors(['error' => 'Failed to capture payment: ' . $captureResult['error']]);
            }

            return back()->with('success', 'Payment captured successfully! Amount: $' . number_format($actualCost, 2));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to capture payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Detect card type based on card number
     */
    private function detectCardType($cardNumber)
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        if (preg_match('/^4/', $cardNumber)) {
            return 'visa';
        } elseif (preg_match('/^5[1-5]/', $cardNumber)) {
            return 'mastercard';
        } elseif (preg_match('/^3[47]/', $cardNumber)) {
            return 'amex';
        } elseif (preg_match('/^6/', $cardNumber)) {
            return 'discover';
        } else {
            return 'unknown';
        }
    }

    private function calculateActualCost(Appointment $appointment)
    {
        $totalMinutes = 0;

        foreach ($appointment->timeLogs as $timeLog) {
            if ($timeLog->started_at && $timeLog->ended_at) {
                $start = Carbon::parse($timeLog->started_at);
                $end = Carbon::parse($timeLog->ended_at);
                $totalMinutes += $end->diffInMinutes($start);
            }
        }

        if ($totalMinutes === 0) {
            return $appointment->estimated_cost; // Fallback to estimated cost
        }

        $hourlyRate = $appointment->serviceType->hourly_rate;
        return ($hourlyRate / 60) * $totalMinutes;
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')
            ->with('success', 'Appointment cancelled successfully!');
    }

    public function availableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after:today',
            'service_type_id' => 'required|exists:service_types,id'
        ]);

        $date = Carbon::parse($request->date);
        $dayOfWeek = strtolower($date->format('l'));
        $serviceType = ServiceType::findOrFail($request->service_type_id);

        // Get all technician availabilities for this day
        $availabilities = TechnicianAvailability::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->whereHas('technician', function($query) use ($serviceType) {
                $query->where('status', 'active')
                      ->whereHas('serviceTypes', function($q) use ($serviceType) {
                          $q->where('service_type_id', $serviceType->id);
                      });
            })
            ->with('technician')
            ->get();

        $timeSlots = [];
        $slotDuration = 30; // 30-minute slots
        $serviceDuration = $serviceType->estimated_duration_minutes;

        foreach ($availabilities as $availability) {
            // Parse start and end times from the availability
            $startTime = Carbon::parse($availability->start_time);
            $endTime = Carbon::parse($availability->end_time);

            // Create a base date for the requested appointment date
            $baseDate = Carbon::parse($request->date);

            // Set the start and end times for this specific date
            $dayStartTime = $baseDate->copy()->setTime($startTime->hour, $startTime->minute, 0);
            $dayEndTime = $baseDate->copy()->setTime($endTime->hour, $endTime->minute, 0);

            // Generate time slots within availability window
            $currentSlot = $dayStartTime->copy();

            while ($currentSlot->copy()->addMinutes($serviceDuration) <= $dayEndTime) {
                $slotEnd = $currentSlot->copy()->addMinutes($serviceDuration);

                // Check if this slot conflicts with existing appointments
                $conflictingAppointment = Appointment::where('status', '!=', 'cancelled')
                    ->where(function($query) use ($currentSlot, $slotEnd, $serviceDuration) {
                        $query->where(function($q) use ($currentSlot, $slotEnd) {
                            $q->where('scheduled_at', '>=', $currentSlot)
                              ->where('scheduled_at', '<', $slotEnd);
                        })->orWhere(function($q) use ($currentSlot, $slotEnd, $serviceDuration) {
                            $q->where('scheduled_at', '<=', $currentSlot)
                              ->where('scheduled_at', '>', $currentSlot->copy()->subMinutes($serviceDuration));
                        });
                    })
                    ->exists();

                if (!$conflictingAppointment) {
                    $timeSlots[] = [
                        'start_time' => $currentSlot->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'datetime' => $currentSlot->format('Y-m-d H:i:s'),
                        'technician_id' => $availability->technician_id,
                        'technician_name' => $availability->technician->user->name,
                        'available' => true
                    ];
                }

                $currentSlot->addMinutes($slotDuration);
            }
        }

        // Sort slots by start time
        usort($timeSlots, function($a, $b) {
            return strtotime($a['start_time']) - strtotime($b['start_time']);
        });

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'service_type' => $serviceType->name,
            'service_duration' => $serviceDuration,
            'time_slots' => $timeSlots
        ]);
    }

    private function notifyAvailableTechnicians(Appointment $appointment)
    {
        $scheduledDate = Carbon::parse($appointment->scheduled_at);
        $dayOfWeek = strtolower($scheduledDate->format('l'));

        $availableTechnicians = TechnicianAvailability::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->whereHas('technician', function($query) {
                $query->where('status', 'active');
            })
            ->get();

        foreach ($availableTechnicians as $availability) {
            // Send SMS notification to technician
            $this->sendSMSNotification($availability->technician, $appointment);
        }
    }

    private function sendSMSNotification($technician, $appointment)
    {
        // Implementation for Twilio SMS
        // This would integrate with your Twilio configuration
        try {
            $client = new \Twilio\Rest\Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            $message = $client->messages->create(
                $technician->phone,
                [
                    'from' => config('services.twilio.from'),
                    'body' => "New appointment request at {$appointment->scheduled_at->format('M j, Y g:i A')}, {$appointment->serviceType->name}, {$appointment->customer_address}. Reply YES to accept."
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send SMS: ' . $e->getMessage());
        }
    }

    public function cancelAppointment(Appointment $appointment)
    {
        try {
            // Check if appointment can be cancelled
            if (!in_array($appointment->status, ['pending', 'confirmed'])) {
                return back()->withErrors(['error' => 'Appointment cannot be cancelled in its current status.']);
            }

            // Release the payment hold if it exists
            if ($appointment->paymentHold && $appointment->paymentHold->status === 'authorized') {
                $cardknoxService = new CardknoxPaymentService();
                $voidResult = $cardknoxService->voidTransaction(
                    $appointment->paymentHold->cardknox_transaction_id
                );

                if ($voidResult['success']) {
                    $appointment->paymentHold->update(['status' => 'voided']);
                } else {
                    \Log::warning('Failed to void payment hold', [
                        'appointment_id' => $appointment->id,
                        'error' => $voidResult['error']
                    ]);
                }
            }

            // Update appointment status
            $appointment->update(['status' => 'cancelled']);

            return redirect()->route('appointments.index')
                ->with('success', 'Appointment cancelled successfully and payment hold released.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to cancel appointment: ' . $e->getMessage()]);
        }
    }
}
