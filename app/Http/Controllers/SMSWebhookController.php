<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Technician;
use App\Models\SMSNotification;
use App\Services\Contracts\AppointmentServiceInterface;
use App\Repositories\Contracts\SMSNotificationRepositoryInterface;
use App\Enums\SMSNotificationType;
use App\Enums\SMSNotificationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SMSWebhookController extends Controller
{
    public function __construct(
        private AppointmentServiceInterface $appointmentService,
        private SMSNotificationRepositoryInterface $smsRepository
    ) {}

    public function handleIncomingSMS(Request $request)
    {
        try {
            $from = $request->input('From');
            $body = trim($request->input('Body', ''));
            $messageSid = $request->input('MessageSid');

            $technician = Technician::where('phone', $from)->first();

            if (!$technician) {
                return response('OK', 200);
            }

            $this->smsRepository->create([
                'appointment_id' => null,
                'technician_id' => $technician->id,
                'type' => SMSNotificationType::RESPONSE_RECEIVED->value,
                'message' => $body,
                'twilio_sid' => $messageSid,
                'status' => SMSNotificationStatus::RECEIVED->value,
                'sent_at' => now()
            ]);

            $this->appointmentService->processTechnicianResponse($technician, $body);

            return response('OK', 200);

        } catch (\Exception $e) {
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
}
