<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Technician;
use App\Repositories\Contracts\SMSNotificationRepositoryInterface;
use App\Services\Contracts\SMSServiceInterface;
use App\Enums\SMSNotificationType;
use App\Enums\SMSNotificationStatus;
use Twilio\Rest\Client;

class SMSService implements SMSServiceInterface
{
    private Client $twilioClient;
    private string $fromNumber;

    public function __construct(
        private SMSNotificationRepositoryInterface $smsRepository
    ) {
        $this->twilioClient = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->fromNumber = config('services.twilio.from');
    }

    public function notifyAvailableTechnicians(Appointment $appointment): int
    {
        $scheduledDate = \Carbon\Carbon::parse($appointment->scheduled_at);
        $dayOfWeek = strtolower($scheduledDate->format('l'));

        $availableTechnicians = \App\Models\TechnicianAvailability::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->whereHas('technician', function($query) use ($appointment) {
                $query->where('status', 'active')
                      ->whereHas('serviceTypes', function($q) use ($appointment) {
                          $q->where('service_type_id', $appointment->service_type_id);
                      });
            })
            ->with('technician.user')
            ->get();

        $notificationsSent = 0;

        foreach ($availableTechnicians as $availability) {
            if ($this->sendAppointmentNotification($availability->technician, $appointment)) {
                $notificationsSent++;
            }
        }

        return $notificationsSent;
    }

    public function sendAppointmentNotification(Technician $technician, Appointment $appointment): bool
    {
        $message = $this->buildAppointmentNotificationMessage($appointment);

        try {
            $twilioMessage = $this->twilioClient->messages->create(
                $technician->phone,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            $this->smsRepository->create([
                'appointment_id' => $appointment->id,
                'technician_id' => $technician->id,
                'type' => SMSNotificationType::APPOINTMENT_NOTIFICATION->value,
                'message' => $message,
                'twilio_sid' => $twilioMessage->sid,
                'status' => SMSNotificationStatus::SENT->value,
                'sent_at' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            $this->smsRepository->create([
                'appointment_id' => $appointment->id,
                'technician_id' => $technician->id,
                'type' => SMSNotificationType::APPOINTMENT_NOTIFICATION->value,
                'message' => $message,
                'status' => SMSNotificationStatus::FAILED->value,
                'error_message' => $e->getMessage(),
                'sent_at' => now()
            ]);

            return false;
        }
    }

    public function sendAcceptanceConfirmation(Technician $technician, Appointment $appointment): bool
    {
        $message = $this->buildAcceptanceConfirmationMessage($appointment);

        try {
            $twilioMessage = $this->twilioClient->messages->create(
                $technician->phone,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            $this->smsRepository->create([
                'appointment_id' => $appointment->id,
                'technician_id' => $technician->id,
                'type' => SMSNotificationType::ACCEPTANCE_CONFIRMATION->value,
                'message' => $message,
                'twilio_sid' => $twilioMessage->sid,
                'status' => SMSNotificationStatus::SENT->value,
                'sent_at' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function notifyAppointmentTaken(Appointment $appointment, Technician $assignedTechnician): void
    {
        $scheduledDate = \Carbon\Carbon::parse($appointment->scheduled_at);
        $dayOfWeek = strtolower($scheduledDate->format('l'));

        $otherTechnicians = \App\Models\TechnicianAvailability::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where('technician_id', '!=', $assignedTechnician->id)
            ->whereHas('technician', function($query) use ($appointment) {
                $query->where('status', 'active')
                      ->whereHas('serviceTypes', function($q) use ($appointment) {
                          $q->where('service_type_id', $appointment->service_type_id);
                      });
            })
            ->with('technician.user')
            ->get();

        $message = $this->buildAppointmentTakenMessage($appointment, $assignedTechnician);

        foreach ($otherTechnicians as $availability) {
            try {
                $this->twilioClient->messages->create(
                    $availability->technician->phone,
                    [
                        'from' => $this->fromNumber,
                        'body' => $message
                    ]
                );

                $this->smsRepository->create([
                    'appointment_id' => $appointment->id,
                    'technician_id' => $availability->technician->id,
                    'type' => SMSNotificationType::APPOINTMENT_TAKEN->value,
                    'message' => $message,
                    'status' => SMSNotificationStatus::SENT->value,
                    'sent_at' => now()
                ]);

            } catch (\Exception $e) {
                // ignore errors
            }
        }
    }

    public function sendAppointmentReminder(Technician $technician, Appointment $appointment): bool
    {
        $scheduledAt = \Carbon\Carbon::parse($appointment->scheduled_at);
        $date = $scheduledAt->format('M j, Y');
        $time = $scheduledAt->format('g:i A');

        $message = "Reminder: You have {$appointment->serviceType->name} appointment in 1 hour at {$time} on {$date}. Location: {$appointment->customer_address}. Customer: {$appointment->customer_name} ({$appointment->customer_phone}).";

        try {
            $twilioMessage = $this->twilioClient->messages->create(
                $technician->phone,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            $this->smsRepository->create([
                'appointment_id' => $appointment->id,
                'technician_id' => $technician->id,
                'type' => SMSNotificationType::APPOINTMENT_REMINDER->value,
                'message' => $message,
                'twilio_sid' => $twilioMessage->sid,
                'status' => SMSNotificationStatus::SENT->value,
                'sent_at' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    private function buildAppointmentNotificationMessage(Appointment $appointment): string
    {
        $scheduledAt = \Carbon\Carbon::parse($appointment->scheduled_at);
        $date = $scheduledAt->format('M j, Y');
        $time = $scheduledAt->format('g:i A');

        return "New appointment: {$appointment->serviceType->name} on {$date} at {$time}. Location: {$appointment->customer_address}. Customer: {$appointment->customer_name} ({$appointment->customer_phone}). Cost: $" . number_format($appointment->estimated_cost, 2) . ". Reply YES to accept.";
    }

    private function buildAcceptanceConfirmationMessage(Appointment $appointment): string
    {
        $scheduledAt = \Carbon\Carbon::parse($appointment->scheduled_at);
        $date = $scheduledAt->format('M j, Y');
        $time = $scheduledAt->format('g:i A');

        return "Appointment confirmed! {$appointment->serviceType->name} on {$date} at {$time}. Location: {$appointment->customer_address}. Customer: {$appointment->customer_name} ({$appointment->customer_phone}). Arrive 10 minutes early.";
    }

    private function buildAppointmentTakenMessage(Appointment $appointment, Technician $assignedTechnician): string
    {
        $scheduledAt = \Carbon\Carbon::parse($appointment->scheduled_at);
        $date = $scheduledAt->format('M j, Y');
        $time = $scheduledAt->format('g:i A');

        return "Appointment for {$appointment->serviceType->name} on {$date} at {$time} taken by {$assignedTechnician->user->name}. Thanks for your availability.";
    }
}
