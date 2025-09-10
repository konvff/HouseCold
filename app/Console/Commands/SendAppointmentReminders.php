<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\Contracts\SMSServiceInterface;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders {--hours=1}';
    protected $description = 'Send SMS reminders to technicians';

    public function __construct(
        private SMSServiceInterface $smsService,
        private AppointmentRepositoryInterface $appointmentRepository
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $hours = $this->option('hours');
        $appointments = $this->appointmentRepository->getUpcomingAppointments($hours);

        if ($appointments->isEmpty()) {
            $this->info('No appointments found.');
            return;
        }

        $sentCount = 0;

        foreach ($appointments as $appointment) {
            try {
                $success = $this->smsService->sendAppointmentReminder($appointment->technician, $appointment);
                if ($success) {
                    $sentCount++;
                    $this->info("Reminder sent to {$appointment->technician->user->name} for appointment #{$appointment->id}");
                }
            } catch (\Exception $e) {
                $this->error("Error sending reminder to {$appointment->technician->user->name}: {$e->getMessage()}");
            }
        }

        $this->info("Reminders sent: {$sentCount}");
    }
}
