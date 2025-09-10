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

    /**
     * Send SMS from technician to user
     */
    public function sendTechnicianToUserMessage(Technician $technician, Appointment $appointment, string $message): bool
    {
        try {
            $twilioMessage = $this->twilioClient->messages->create(
                $appointment->customer_phone,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            $this->smsRepository->create([
                'appointment_id' => $appointment->id,
                'technician_id' => $technician->id,
                'user_id' => $appointment->user_id,
                'type' => SMSNotificationType::TECHNICIAN_TO_USER->value,
                'message' => $message,
                'phone_number' => $appointment->customer_phone,
                'direction' => 'outbound',
                'twilio_sid' => $twilioMessage->sid,
                'status' => SMSNotificationStatus::SENT->value,
                'sent_at' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            $this->smsRepository->create([
                'appointment_id' => $appointment->id,
                'technician_id' => $technician->id,
                'user_id' => $appointment->user_id,
                'type' => SMSNotificationType::TECHNICIAN_TO_USER->value,
                'message' => $message,
                'phone_number' => $appointment->customer_phone,
                'direction' => 'outbound',
                'status' => SMSNotificationStatus::FAILED->value,
                'error_message' => $e->getMessage(),
                'sent_at' => now()
            ]);

            return false;
        }
    }

    /**
     * Send SMS from user to technician
     */
    public function sendUserToTechnicianMessage(User $user, Technician $technician, Appointment $appointment, string $message): bool
    {
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
                'user_id' => $user->id,
                'type' => SMSNotificationType::USER_TO_TECHNICIAN->value,
                'message' => $message,
                'phone_number' => $technician->phone,
                'direction' => 'outbound',
                'twilio_sid' => $twilioMessage->sid,
                'status' => SMSNotificationStatus::SENT->value,
                'sent_at' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            $this->smsRepository->create([
                'appointment_id' => $appointment->id,
                'technician_id' => $technician->id,
                'user_id' => $user->id,
                'type' => SMSNotificationType::USER_TO_TECHNICIAN->value,
                'message' => $message,
                'phone_number' => $technician->phone,
                'direction' => 'outbound',
                'status' => SMSNotificationStatus::FAILED->value,
                'error_message' => $e->getMessage(),
                'sent_at' => now()
            ]);

            return false;
        }
    }

    /**
     * Process incoming SMS reply
     */
    public function processIncomingSMS(string $from, string $to, string $body, string $twilioSid): void
    {
        // Find the appointment and technician based on phone numbers
        $appointment = \App\Models\Appointment::where('customer_phone', $from)
            ->orWhere('customer_phone', $this->normalizePhoneNumber($from))
            ->first();

        if (!$appointment) {
            return;
        }

        $technician = \App\Models\Technician::where('phone', $to)
            ->orWhere('phone', $this->normalizePhoneNumber($to))
            ->first();

        if (!$technician) {
            return;
        }

        // Determine if this is a user reply or technician reply
        $isUserReply = $appointment->customer_phone === $from || $appointment->customer_phone === $this->normalizePhoneNumber($from);
        $type = $isUserReply ? SMSNotificationType::USER_REPLY : SMSNotificationType::TECHNICIAN_REPLY;

        // Save the incoming message
        $this->smsRepository->create([
            'appointment_id' => $appointment->id,
            'technician_id' => $technician->id,
            'user_id' => $appointment->user_id,
            'type' => $type->value,
            'message' => $body,
            'phone_number' => $from,
            'direction' => 'inbound',
            'twilio_sid' => $twilioSid,
            'status' => SMSNotificationStatus::SENT->value,
            'sent_at' => now(),
            'delivered_at' => now()
        ]);

        // Handle special commands
        $this->handleSpecialCommands($appointment, $technician, $body, $isUserReply);
    }

    /**
     * Handle special SMS commands
     */
    private function handleSpecialCommands(Appointment $appointment, Technician $technician, string $message, bool $isUserReply): void
    {
        $message = strtoupper(trim($message));

        if ($isUserReply) {
            // Handle user commands
            switch ($message) {
                case 'YES':
                case 'ACCEPT':
                case 'CONFIRM':
                    // User confirming appointment
                    $this->sendAcceptanceConfirmation($technician, $appointment);
                    break;
                case 'NO':
                case 'CANCEL':
                case 'DECLINE':
                    // User declining appointment
                    $this->sendCancellationNotification($appointment, $technician);
                    break;
            }
        } else {
            // Handle technician commands
            switch ($message) {
                case 'YES':
                case 'ACCEPT':
                    // Technician accepting appointment
                    $appointment->update(['technician_id' => $technician->id, 'status' => 'confirmed']);
                    $this->sendAcceptanceConfirmation($technician, $appointment);
                    $this->notifyAppointmentTaken($appointment, $technician);
                    break;
                case 'NO':
                case 'DECLINE':
                    // Technician declining appointment
                    $this->sendDeclineNotification($appointment, $technician);
                    break;
            }
        }
    }

    /**
     * Send cancellation notification
     */
    private function sendCancellationNotification(Appointment $appointment, Technician $technician): void
    {
        $message = "Appointment #{$appointment->id} has been cancelled by the customer. Thank you for your availability.";
        
        try {
            $this->twilioClient->messages->create(
                $technician->phone,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            $this->smsRepository->create([
                'appointment_id' => $appointment->id,
                'technician_id' => $technician->id,
                'user_id' => $appointment->user_id,
                'type' => SMSNotificationType::APPOINTMENT_TAKEN->value,
                'message' => $message,
                'phone_number' => $technician->phone,
                'direction' => 'outbound',
                'status' => SMSNotificationStatus::SENT->value,
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail
        }
    }

    /**
     * Send decline notification
     */
    private function sendDeclineNotification(Appointment $appointment, Technician $technician): void
    {
        $message = "Technician {$technician->user->name} has declined appointment #{$appointment->id}. We'll find another technician for you.";
        
        try {
            $this->twilioClient->messages->create(
                $appointment->customer_phone,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            $this->smsRepository->create([
                'appointment_id' => $appointment->id,
                'technician_id' => $technician->id,
                'user_id' => $appointment->user_id,
                'type' => SMSNotificationType::APPOINTMENT_TAKEN->value,
                'message' => $message,
                'phone_number' => $appointment->customer_phone,
                'direction' => 'outbound',
                'status' => SMSNotificationStatus::SENT->value,
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail
        }
    }

    /**
     * Normalize phone number format
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add +1 if it's a 10-digit US number
        if (strlen($phone) === 10) {
            $phone = '+1' . $phone;
        }
        
        return $phone;
    }
}
