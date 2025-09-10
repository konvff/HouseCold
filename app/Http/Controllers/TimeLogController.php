<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Services\Contracts\TimeLogServiceInterface;
use App\Enums\AppointmentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeLogController extends Controller
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private TimeLogServiceInterface $timeLogService
    ) {}
    public function index()
    {
        $timeLogs = TimeLog::with(['appointment', 'technician'])
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        return view('time-logs.index', compact('timeLogs'));
    }

    public function startTimer(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'notes' => 'nullable|string'
        ]);

        $appointment = $this->appointmentRepository->find($request->appointment_id);
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        $technician = Auth::user()->technician;
        if (!$technician || $appointment->technician_id !== $technician->id) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        if ($appointment->status !== AppointmentStatus::CONFIRMED->value) {
            return response()->json(['error' => 'Appointment must be confirmed to start timer'], 400);
        }

        try {
            $timeLog = $this->timeLogService->startTimer($appointment);
            $this->appointmentRepository->update($appointment->id, ['status' => AppointmentStatus::IN_PROGRESS->value]);

            return response()->json([
                'success' => true,
                'message' => 'Timer started successfully',
                'time_log_id' => $timeLog->id,
                'started_at' => $timeLog->started_at
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function stopTimer(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'notes' => 'nullable|string'
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $technician = Auth::user()->technician;

        if (!$technician || $appointment->technician_id !== $technician->id) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $timeLog = $appointment->getCurrentTimeLog();
        if (!$timeLog) {
            return response()->json(['error' => 'No active timer found for this appointment'], 400);
        }

        // Stop timer and calculate duration
        $endedAt = now();
        $durationMinutes = $timeLog->started_at->diffInMinutes($endedAt);

        $timeLog->update([
            'ended_at' => $endedAt,
            'duration_minutes' => $durationMinutes,
            'notes' => $request->notes ? $timeLog->notes . "\n" . $request->notes : $timeLog->notes
        ]);

        $appointment->update(['status' => 'completed']);

        // Calculate actual cost
        $durationHours = $durationMinutes / 60;
        $actualCost = $appointment->serviceType->hourly_rate * $durationHours;

        $appointment->update(['actual_cost' => $actualCost]);

        return response()->json([
            'success' => true,
            'message' => 'Timer stopped successfully',
            'duration_minutes' => $durationMinutes,
            'duration_hours' => round($durationHours, 2),
            'actual_cost' => $actualCost
        ]);
    }

    public function pauseTimer(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'notes' => 'nullable|string'
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $technician = Auth::user()->technician;

        if (!$technician || $appointment->technician_id !== $technician->id) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $timeLog = $appointment->getCurrentTimeLog();
        if (!$timeLog) {
            return response()->json(['error' => 'No active timer found for this appointment'], 400);
        }

        // Pause timer by stopping current session
        $endedAt = now();
        $durationMinutes = $timeLog->started_at->diffInMinutes($endedAt);

        $timeLog->update([
            'ended_at' => $endedAt,
            'duration_minutes' => $durationMinutes,
            'notes' => $request->notes ? $timeLog->notes . "\n[PAUSED] " . $request->notes : $timeLog->notes . "\n[PAUSED]"
        ]);

        $appointment->update(['status' => 'confirmed']);

        return response()->json([
            'success' => true,
            'message' => 'Timer paused successfully',
            'duration_minutes' => $durationMinutes
        ]);
    }

    public function resumeTimer(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'notes' => 'nullable|string'
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $technician = Auth::user()->technician;

        if (!$technician || $appointment->technician_id !== $technician->id) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        if ($appointment->status !== 'confirmed') {
            return response()->json(['error' => 'Appointment must be confirmed to resume timer'], 400);
        }

        // Start new timer session
        $timeLog = TimeLog::create([
            'appointment_id' => $appointment->id,
            'technician_id' => $technician->id,
            'started_at' => now(),
            'notes' => $request->notes ? "[RESUMED] " . $request->notes : "[RESUMED]"
        ]);

        $appointment->update(['status' => 'in_progress']);

        return response()->json([
            'success' => true,
            'message' => 'Timer resumed successfully',
            'time_log_id' => $timeLog->id,
            'started_at' => $timeLog->started_at
        ]);
    }

    public function getCurrentTimer(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id'
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $timeLog = $appointment->getCurrentTimeLog();

        if (!$timeLog) {
            return response()->json(['error' => 'No active timer found'], 404);
        }

        $elapsedMinutes = $timeLog->started_at->diffInMinutes(now());
        $elapsedHours = round($elapsedMinutes / 60, 2);

        return response()->json([
            'time_log_id' => $timeLog->id,
            'started_at' => $timeLog->started_at,
            'elapsed_minutes' => $elapsedMinutes,
            'elapsed_hours' => $elapsedHours,
            'notes' => $timeLog->notes
        ]);
    }

    public function updateNotes(Request $request, TimeLog $timeLog)
    {
        $request->validate([
            'notes' => 'required|string'
        ]);

        $technician = Auth::user()->technician;
        if (!$technician || $timeLog->technician_id !== $technician->id) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $timeLog->update(['notes' => $request->notes]);

        return response()->json([
            'success' => true,
            'message' => 'Notes updated successfully'
        ]);
    }
}
