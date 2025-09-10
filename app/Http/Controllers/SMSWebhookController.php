<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Technician;
use App\Models\SMSNotification;
use App\Models\User;
use App\Services\Contracts\AppointmentServiceInterface;
use App\Services\Contracts\SMSServiceInterface;
use App\Repositories\Contracts\SMSNotificationRepositoryInterface;
use App\Enums\SMSNotificationType;
use App\Enums\SMSNotificationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SMSWebhookController extends Controller
{
    public function __construct(
        private AppointmentServiceInterface $appointmentService,
        private SMSServiceInterface $smsService,
        private SMSNotificationRepositoryInterface $smsRepository
    ) {}

    public function handleIncomingSMS(Request $request)
    {
        try {
            $from = $request->input('From');
            $to = $request->input('To');
            $body = trim($request->input('Body', ''));
            $messageSid = $request->input('MessageSid');

            // Use the enhanced SMS service to process incoming messages
            $this->smsService->processIncomingSMS($from, $to, $body, $messageSid);

            return response('OK', 200);

        } catch (\Exception $e) {
            \Log::error('SMS Webhook Error: ' . $e->getMessage());
            return response('Error', 500);
        }
    }


    public function handleDeliveryStatus(Request $request)
    {
        $messageSid = $request->input('MessageSid');
        $status = $request->input('MessageStatus');

        if ($messageSid) {
            $this->smsRepository->updateStatus($messageSid, $status);
        }

        return response('OK', 200);
    }

    /**
     * Send SMS from technician to user
     */
    public function sendTechnicianMessage(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'message' => 'required|string|max:1600'
        ]);

        try {
            $appointment = Appointment::findOrFail($request->appointment_id);
            $technician = auth()->user()->technician;

            if (!$technician) {
                return response()->json(['error' => 'Technician not found'], 404);
            }

            $success = $this->smsService->sendTechnicianToUserMessage(
                $technician,
                $appointment,
                $request->message
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send SMS'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send SMS from user to technician
     */
    public function sendUserMessage(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'message' => 'required|string|max:1600'
        ]);

        try {
            $appointment = Appointment::findOrFail($request->appointment_id);
            $user = auth()->user();

            if (!$appointment->technician) {
                return response()->json(['error' => 'No technician assigned to this appointment'], 404);
            }

            $success = $this->smsService->sendUserToTechnicianMessage(
                $user,
                $appointment->technician,
                $appointment,
                $request->message
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send SMS'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get SMS conversation for an appointment
     */
    public function getConversation(Request $request, $appointmentId)
    {
        try {
            $appointment = Appointment::findOrFail($appointmentId);

            // Check if user has access to this appointment
            if (auth()->user()->isAdmin() ||
                (auth()->user()->technician && $appointment->technician_id === auth()->user()->technician->id) ||
                $appointment->user_id === auth()->id()) {

                $messages = SMSNotification::where('appointment_id', $appointmentId)
                    ->orderBy('created_at', 'asc')
                    ->get();

                return response()->json([
                    'success' => true,
                    'messages' => $messages
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
