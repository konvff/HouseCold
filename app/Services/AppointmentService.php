<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Technician;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\SMSNotificationRepositoryInterface;
use App\Services\Contracts\AppointmentServiceInterface;
use App\Services\Contracts\SMSServiceInterface;
use App\Enums\AppointmentStatus;
use App\Enums\SMSNotificationType;
use App\Enums\SMSNotificationStatus;
use Illuminate\Support\Facades\DB;

class AppointmentService implements AppointmentServiceInterface
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private SMSNotificationRepositoryInterface $smsRepository,
        private SMSServiceInterface $smsService
    ) {}

    public function createAppointment(array $data): Appointment
    {
        return $this->appointmentRepository->create($data);
    }

    public function assignTechnician(Appointment $appointment, Technician $technician): bool
    {
        try {
            DB::beginTransaction();

            $success = $this->appointmentRepository->assignTechnician($appointment->id, $technician->id);

            if ($success) {
                $this->smsService->sendAcceptanceConfirmation($technician, $appointment);
                $this->smsService->notifyAppointmentTaken($appointment, $technician);

                $this->smsRepository->create([
                    'appointment_id' => $appointment->id,
                    'technician_id' => $technician->id,
                    'type' => SMSNotificationType::RESPONSE_RECEIVED->value,
                    'message' => 'ACCEPTED',
                    'status' => SMSNotificationStatus::RECEIVED->value,
                    'sent_at' => now()
                ]);
            }

            DB::commit();
            return $success;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function processTechnicianResponse(Technician $technician, string $response): void
    {
        $response = strtolower(trim($response));

        if ($this->isPositiveResponse($response)) {
            $this->handleAcceptance($technician);
        } elseif ($this->isNegativeResponse($response)) {
            $this->handleDecline($technician);
        } elseif ($this->isStatusRequest($response)) {
            $this->handleStatusRequest($technician);
        } else {
            $this->handleUnknownResponse($technician, $response);
        }
    }

    private function handleAcceptance(Technician $technician): void
    {
        $appointment = $this->appointmentRepository->findPendingForTechnician($technician->id);

        if (!$appointment) {
            $this->sendNoPendingAppointmentMessage($technician);
            return;
        }

        if ($appointment->technician_id) {
            $this->sendAppointmentAlreadyTakenMessage($technician);
            return;
        }

        $this->assignTechnician($appointment, $technician);
    }

    private function handleDecline(Technician $technician): void
    {
        $appointment = $this->appointmentRepository->findPendingForTechnician($technician->id);

        if ($appointment) {
            $this->smsRepository->create([
                'appointment_id' => $appointment->id,
                'technician_id' => $technician->id,
                'type' => SMSNotificationType::RESPONSE_RECEIVED->value,
                'message' => 'DECLINED',
                'status' => SMSNotificationStatus::RECEIVED->value,
                'sent_at' => now()
            ]);
        }

        $this->sendDeclineConfirmationMessage($technician);
    }

    private function handleStatusRequest(Technician $technician): void
    {
        $todayAppointments = Appointment::where('technician_id', $technician->id)
            ->whereDate('scheduled_at', today())
            ->whereIn('status', [AppointmentStatus::CONFIRMED->value, AppointmentStatus::IN_PROGRESS->value])
            ->with('serviceType')
            ->get();

        if ($todayAppointments->isEmpty()) {
            $message = "No appointments today.";
        } else {
            $message = "Today's appointments: ";
            foreach ($todayAppointments as $appointment) {
                $time = \Carbon\Carbon::parse($appointment->scheduled_at)->format('g:i A');
                $message .= "{$time} - {$appointment->serviceType->name} at {$appointment->customer_address}. ";
            }
        }

        $this->sendSMS($technician->phone, $message);
    }

    private function handleUnknownResponse(Technician $technician, string $response): void
    {
        $message = "Didn't understand '{$response}'. Reply YES to accept, NO to decline, or STATUS for appointments.";
        $this->sendSMS($technician->phone, $message);
    }

    private function isPositiveResponse(string $response): bool
    {
        $positiveResponses = ['yes', 'y', 'accept', 'ok', 'sure', 'confirm'];
        return in_array($response, $positiveResponses);
    }

    private function isNegativeResponse(string $response): bool
    {
        $negativeResponses = ['no', 'n', 'decline', 'reject', 'pass'];
        return in_array($response, $negativeResponses);
    }

    private function isStatusRequest(string $response): bool
    {
        $statusRequests = ['status', 'schedule', 'appointments', 'today'];
        return in_array($response, $statusRequests);
    }

    private function sendNoPendingAppointmentMessage(Technician $technician): void
    {
        $message = "No pending appointments. Reply STATUS for current appointments.";
        $this->sendSMS($technician->phone, $message);
    }

    private function sendAppointmentAlreadyTakenMessage(Technician $technician): void
    {
        $message = "Appointment already taken by another technician.";
        $this->sendSMS($technician->phone, $message);
    }

    private function sendDeclineConfirmationMessage(Technician $technician): void
    {
        $message = "Thanks for letting us know.";
        $this->sendSMS($technician->phone, $message);
    }

    private function sendSMS(string $phone, string $message): void
    {
        try {
            $client = new \Twilio\Rest\Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            $client->messages->create(
                $phone,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message
                ]
            );

        } catch (\Exception $e) {
            // ignore errors
        }
    }
}
