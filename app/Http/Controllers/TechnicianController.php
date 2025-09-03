<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\TechnicianAvailability;
use App\Models\Appointment;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CardknoxPaymentService;

class TechnicianController extends Controller
{
    public function index()
    {
        $technicians = Technician::with(['user', 'availabilities', 'serviceTypes'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('technicians.index', compact('technicians'));
    }

    public function create()
    {
        $serviceTypes = ServiceType::where('is_active', true)->get();
        return view('technicians.create', compact('serviceTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'specialties' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,on_leave',
            'password' => 'required|string|min:8',
            'service_types' => 'nullable|array'
        ]);

        // Create user first
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'technician'
        ]);

        // Process specialties - convert comma-separated string to array
        $specialties = null;
        if ($request->specialties) {
            $specialties = array_map('trim', explode(',', $request->specialties));
            $specialties = array_filter($specialties); // Remove empty values
        }

        // Create technician
        $technician = Technician::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'specialties' => $specialties,
            'hourly_rate' => $request->hourly_rate,
            'status' => $request->status
        ]);

        // Attach service types if provided
        if ($request->service_types) {
            $technician->serviceTypes()->attach($request->service_types);
        }

        return redirect()->route('technicians.index')
            ->with('success', 'Technician created successfully!');
    }

    public function show(Technician $technician)
    {
        $technician->load(['user', 'availabilities', 'appointments', 'serviceTypes']);
        return view('technicians.show', compact('technician'));
    }

    public function edit(Technician $technician)
    {
        $serviceTypes = ServiceType::where('is_active', true)->get();
        return view('technicians.edit', compact('technician', 'serviceTypes'));
    }

    public function update(Request $request, Technician $technician)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $technician->user_id,
            'phone' => 'required|string|max:20',
            'specialties' => 'nullable|string',
            'status' => 'required|in:active,inactive,on_leave',
            'hourly_rate' => 'nullable|numeric|min:0',
            'password' => 'nullable|string|min:8',
            'service_types' => 'nullable|array'
        ]);

        // Update user information
        $technician->user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        // Update password if provided
        if ($request->password) {
            $technician->user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        // Process specialties - convert comma-separated string to array
        $specialties = null;
        if ($request->specialties) {
            $specialties = array_map('trim', explode(',', $request->specialties));
            $specialties = array_filter($specialties); // Remove empty values
        }

        // Update technician
        $technician->update([
            'phone' => $request->phone,
            'specialties' => $specialties,
            'hourly_rate' => $request->hourly_rate,
            'status' => $request->status
        ]);

        // Sync service types
        if ($request->service_types) {
            $technician->serviceTypes()->sync($request->service_types);
        } else {
            $technician->serviceTypes()->detach();
        }

        return redirect()->route('technicians.show', $technician)
            ->with('success', 'Technician updated successfully!');
    }

    public function destroy(Technician $technician)
    {
        $technician->delete();
        return redirect()->route('technicians.index')
            ->with('success', 'Technician removed successfully!');
    }

    public function dashboard()
    {
        $technician = Auth::user()->technician;

        if (!$technician) {
            return redirect()->route('home')->withErrors(['error' => 'Access denied. Technician account required.']);
        }

        $pendingAppointments = Appointment::where('status', 'pending')
            ->whereHas('serviceType', function($query) use ($technician) {
                // Check if technician is available for these appointments
                $query->whereHas('technicianAvailabilities', function($q) use ($technician) {
                    $q->where('technician_id', $technician->id);
                });
            })
            ->get();

        $confirmedAppointments = Appointment::where('technician_id', $technician->id)
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $completedAppointments = Appointment::where('technician_id', $technician->id)
            ->where('status', 'completed')
            ->orderBy('scheduled_at', 'desc')
            ->limit(10)
            ->get();

        return view('technicians.dashboard', compact(
            'technician',
            'pendingAppointments',
            'confirmedAppointments',
            'completedAppointments'
        ));
    }

    public function acceptAppointment(Appointment $appointment)
    {
        $technician = Auth::user()->technician;

        if (!$technician) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        // Check if appointment is still pending
        if ($appointment->status !== 'pending') {
            return back()->withErrors(['error' => 'Appointment is no longer available.']);
        }

        // Check if technician is available for this time slot
        $scheduledDate = $appointment->scheduled_at;
        $dayOfWeek = strtolower($scheduledDate->format('l'));

        $isAvailable = TechnicianAvailability::where('technician_id', $technician->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where('start_time', '<=', $scheduledDate->format('H:i:s'))
            ->where('end_time', '>=', $scheduledDate->format('H:i:s'))
            ->exists();

        if (!$isAvailable) {
            return back()->withErrors(['error' => 'You are not available for this time slot.']);
        }

        // Accept the appointment
        $appointment->update([
            'technician_id' => $technician->id,
            'status' => 'confirmed'
        ]);

        // Send confirmation to customer (email/SMS)
        // $this->sendCustomerConfirmation($appointment);

        return back()->with('success', 'Appointment accepted successfully!');
    }

    public function declineAppointment(Appointment $appointment)
    {
        $technician = Auth::user()->technician;

        if (!$technician) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        // Log the decline for tracking
        \Log::info("Technician {$technician->id} declined appointment {$appointment->id}");

        return back()->with('success', 'Appointment declined.');
    }

    public function startTimer(Appointment $appointment)
    {
        $technician = Auth::user()->technician;

        if (!$technician || $appointment->technician_id !== $technician->id) {
            return response()->json(['success' => false, 'error' => 'Access denied.'], 403);
        }

        if ($appointment->status !== 'confirmed') {
            return response()->json(['success' => false, 'error' => 'Appointment must be confirmed to start timer.'], 400);
        }

        // Check if timer is already running
        $existingTimer = $appointment->getCurrentTimeLog();
        if ($existingTimer) {
            return response()->json(['success' => false, 'error' => 'Timer is already running for this appointment.'], 400);
        }

        // Start timer
        $timeLog = $appointment->timeLogs()->create([
            'technician_id' => $technician->id,
            'started_at' => now()
        ]);

        $appointment->update(['status' => 'in_progress']);

        return response()->json([
            'success' => true,
            'message' => 'Timer started successfully!',
            'started_at' => $timeLog->started_at->toISOString()
        ]);
    }

    public function stopTimer(Appointment $appointment)
    {
        $technician = Auth::user()->technician;

        if (!$technician || $appointment->technician_id !== $technician->id) {
            return response()->json(['success' => false, 'error' => 'Access denied.'], 403);
        }

        $timeLog = $appointment->getCurrentTimeLog();
        if (!$timeLog) {
            return response()->json(['success' => false, 'error' => 'No active timer found.'], 400);
        }

        // Stop timer and calculate duration
        $endedAt = now();
        $durationMinutes = $timeLog->started_at->diffInMinutes($endedAt);

        $timeLog->update([
            'ended_at' => $endedAt,
            'duration_minutes' => $durationMinutes
        ]);

        $appointment->update(['status' => 'completed']);

        // Calculate and update actual cost
        $durationHours = $durationMinutes / 60;
        $actualCost = $appointment->serviceType->hourly_rate * $durationHours;
        $appointment->update(['actual_cost' => $actualCost]);

        // Auto-capture payment using the centralized method
        $this->autoCapturePayment($appointment);

        return response()->json([
            'success' => true,
            'message' => 'Timer stopped and appointment completed!',
            'duration_minutes' => $durationMinutes,
            'duration_hours' => round($durationHours, 2),
            'actual_cost' => $actualCost
        ]);
    }

    public function pauseTimer(Appointment $appointment)
    {
        $technician = Auth::user()->technician;

        if (!$technician || $appointment->technician_id !== $technician->id) {
            return response()->json(['success' => false, 'error' => 'Access denied.'], 403);
        }

        $timeLog = $appointment->getCurrentTimeLog();
        if (!$timeLog) {
            return response()->json(['success' => false, 'error' => 'No active timer found.'], 400);
        }

        // Pause timer by stopping current session
        $endedAt = now();
        $durationMinutes = $timeLog->started_at->diffInMinutes($endedAt);

        $timeLog->update([
            'ended_at' => $endedAt,
            'duration_minutes' => $durationMinutes,
            'notes' => $timeLog->notes ? $timeLog->notes . "\n[PAUSED]" : "[PAUSED]"
        ]);

        $appointment->update(['status' => 'confirmed']);

        return response()->json([
            'success' => true,
            'message' => 'Timer paused successfully!',
            'duration_minutes' => $durationMinutes
        ]);
    }

    private function autoCapturePayment(Appointment $appointment)
    {
        try {
            // Check if appointment has authorized payment
            $paymentHold = $appointment->paymentHold;
            if (!$paymentHold || $paymentHold->status !== 'authorized') {
                \Log::warning('No authorized payment found for completed appointment', [
                    'appointment_id' => $appointment->id,
                    'payment_status' => $paymentHold ? $paymentHold->status : 'no_payment_hold'
                ]);
                return;
            }

            // Prevent double capture
            if ($paymentHold->status === 'captured') {
                \Log::info('Payment already captured for appointment', [
                    'appointment_id' => $appointment->id,
                    'payment_status' => $paymentHold->status
                ]);
                return;
            }

            // Use the actual cost that was already calculated by TimeLogController
            // If no actual cost exists, fall back to estimated cost
            $actualCost = $appointment->actual_cost ?: $this->calculateActualCost($appointment);
            $chargeAmount = min($actualCost, $paymentHold->amount); // Don't exceed authorized amount

            \Log::info('Auto-capturing payment for completed appointment', [
                'appointment_id' => $appointment->id,
                'estimated_cost' => $paymentHold->amount,
                'actual_cost' => $actualCost,
                'charge_amount' => $chargeAmount,
                'note' => 'Using TimeLog-calculated actual cost (capped at authorized amount)'
            ]);

            // Capture the payment using Cardknox
            $cardknoxService = new CardknoxPaymentService();
            $captureResult = $cardknoxService->capturePayment(
                $paymentHold->cardknox_transaction_id,
                $chargeAmount,
                'APPOINTMENT_' . $appointment->id
            );

            if ($captureResult['success']) {
                // Update payment hold status
                $paymentHold->update([
                    'status' => 'captured',
                    'amount' => $chargeAmount,
                    'captured_at' => now()
                ]);

                // Keep the actual cost that was calculated by TimeLogController
                // Don't override it here

                \Log::info('Payment auto-captured successfully via Cardknox', [
                    'appointment_id' => $appointment->id,
                    'amount' => $chargeAmount,
                    'cardknox_transaction_id' => $paymentHold->cardknox_transaction_id
                ]);

                // Send confirmation to customer
                $this->sendPaymentConfirmation($appointment, $chargeAmount);
            } else {
                \Log::error('Failed to capture payment via Cardknox', [
                    'appointment_id' => $appointment->id,
                    'error' => $captureResult['error'],
                    'error_code' => $captureResult['error_code']
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to auto-capture payment', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);

            // Don't fail the appointment completion, just log the error
            // Admin can manually capture payment later if needed
        }
    }

    private function calculateActualCost(Appointment $appointment)
    {
        $totalMinutes = 0;

        foreach ($appointment->timeLogs as $timeLog) {
            if ($timeLog->started_at && $timeLog->ended_at) {
                $start = \Carbon\Carbon::parse($timeLog->started_at);
                $end = \Carbon\Carbon::parse($timeLog->ended_at);
                $totalMinutes += $end->diffInMinutes($start);
            }
        }

        if ($totalMinutes === 0) {
            return $appointment->estimated_cost; // Fallback to estimated cost
        }

        $hourlyRate = $appointment->serviceType->hourly_rate;
        return ($hourlyRate / 60) * $totalMinutes;
    }

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


    public function confirmedJobs()
    {
        $technician = Auth::user()->technician;
        if (!$technician) {
            return redirect()->route('home')->withErrors(['error' => 'Access denied. Technician account required.']);
        }

        $confirmedAppointments = Appointment::where('technician_id', $technician->id)
            ->where('status', 'confirmed')
            ->with(['serviceType', 'paymentHold'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $serviceTypes = \App\Models\ServiceType::where('is_active', true)->get();

        // Counts for sidebar
        $pendingCount = Appointment::where('status', 'pending')
            ->whereHas('serviceType', function($query) use ($technician) {
                $query->whereHas('technicianAvailabilities', function($q) use ($technician) {
                    $q->where('technician_id', $technician->id);
                });
            })->count();
        $confirmedCount = $confirmedAppointments->count();
        $inProgressCount = Appointment::where('technician_id', $technician->id)
            ->where('status', 'in_progress')->count();

        return view('technicians.confirmed-jobs', compact(
            'confirmedAppointments',
            'serviceTypes',
            'pendingCount',
            'confirmedCount',
            'inProgressCount'
        ));
    }

    public function inProgressJobs()
    {
        $technician = Auth::user()->technician;
        if (!$technician) {
            return redirect()->route('home')->withErrors(['error' => 'Access denied. Technician account required.']);
        }

        $inProgressAppointments = Appointment::where('technician_id', $technician->id)
            ->where('status', 'in_progress')
            ->with(['serviceType', 'paymentHold', 'timeLogs'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $serviceTypes = \App\Models\ServiceType::where('is_active', true)->get();

        // Counts for sidebar
        $pendingCount = Appointment::where('status', 'pending')
            ->whereHas('serviceType', function($query) use ($technician) {
                $query->whereHas('technicianAvailabilities', function($q) use ($technician) {
                    $q->where('technician_id', $technician->id);
                });
            })->count();
        $confirmedCount = Appointment::where('technician_id', $technician->id)
            ->where('status', 'confirmed')->count();
        $inProgressCount = $inProgressAppointments->count();

        return view('technicians.in-progress-jobs', compact(
            'inProgressAppointments',
            'serviceTypes',
            'pendingCount',
            'confirmedCount',
            'inProgressCount'
        ));
    }

    public function completedJobs()
    {
        $technician = Auth::user()->technician;
        if (!$technician) {
            return redirect()->route('home')->withErrors(['error' => 'Access denied. Technician account required.']);
        }

        $completedAppointments = Appointment::where('technician_id', $technician->id)
            ->where('status', 'completed')
            ->with(['serviceType', 'paymentHold', 'timeLogs'])
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();

        $serviceTypes = \App\Models\ServiceType::where('is_active', true)->get();

        // Calculate summary stats
        $totalEarnings = $completedAppointments->sum('actual_cost');
        $totalMinutes = $completedAppointments->sum(function($appointment) {
            return $appointment->timeLogs->sum('duration_minutes');
        });
        $totalHours = floor($totalMinutes / 60);
        $remainingMinutes = $totalMinutes % 60;
        $averageHourlyRate = $totalHours > 0 ? $totalEarnings / $totalHours : 0;

        // Counts for sidebar
        $pendingCount = Appointment::where('status', 'pending')
            ->whereHas('serviceType', function($query) use ($technician) {
                $query->whereHas('technicianAvailabilities', function($q) use ($technician) {
                    $q->where('technician_id', $technician->id);
                });
            })->count();
        $confirmedCount = Appointment::where('technician_id', $technician->id)
            ->where('status', 'confirmed')->count();
        $inProgressCount = Appointment::where('technician_id', $technician->id)
            ->where('status', 'in_progress')->count();

        return view('technicians.completed-jobs', compact(
            'completedAppointments',
            'serviceTypes',
            'totalEarnings',
            'totalHours',
            'remainingMinutes',
            'averageHourlyRate',
            'pendingCount',
            'confirmedCount',
            'inProgressCount'
        ));
    }

    public function profile()
    {
        $technician = Auth::user()->technician;
        if (!$technician) {
            return redirect()->route('home')->withErrors(['error' => 'Access denied. Technician account required.']);
        }

        $serviceTypes = \App\Models\ServiceType::where('is_active', true)->get();

        // Counts for sidebar
        $pendingCount = Appointment::where('status', 'pending')
            ->whereHas('serviceType', function($query) use ($technician) {
                $query->whereHas('technicianAvailabilities', function($q) use ($technician) {
                    $q->where('technician_id', $technician->id);
                });
            })->count();
        $confirmedCount = Appointment::where('technician_id', $technician->id)
            ->where('status', 'confirmed')->count();
        $inProgressCount = Appointment::where('technician_id', $technician->id)
            ->where('status', 'in_progress')->count();

        return view('technicians.profile', compact(
            'technician',
            'serviceTypes',
            'pendingCount',
            'confirmedCount',
            'inProgressCount'
        ));
    }
}
