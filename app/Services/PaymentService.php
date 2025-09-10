<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\PaymentHold;
use App\Repositories\Contracts\PaymentHoldRepositoryInterface;
use App\Services\Contracts\PaymentServiceInterface;
use App\Services\CardknoxPaymentService;
use App\Enums\PaymentStatus;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        private PaymentHoldRepositoryInterface $paymentHoldRepository,
        private CardknoxPaymentService $cardknoxService
    ) {}

    public function authorizePayment(array $paymentData, array $cardData, array $customerInfo): array
    {
        return $this->cardknoxService->authorizePayment(
            $paymentData['amount'],
            $cardData,
            $customerInfo,
            $paymentData['description']
        );
    }

    public function capturePayment(string $transactionId, float $amount, string $description): array
    {
        return $this->cardknoxService->capturePayment($transactionId, $amount, $description);
    }

    public function voidTransaction(string $transactionId): array
    {
        return $this->cardknoxService->voidTransaction($transactionId);
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        return $this->cardknoxService->refundPayment($transactionId, $amount);
    }

    public function createPaymentHold(Appointment $appointment, array $paymentData): PaymentHold
    {
        return $this->paymentHoldRepository->create([
            'appointment_id' => $appointment->id,
            'amount' => $paymentData['amount'],
            'status' => PaymentStatus::AUTHORIZED->value,
            'cardknox_transaction_id' => $paymentData['transaction_id'],
            'cardknox_auth_code' => $paymentData['auth_code'],
            'card_last_four' => $paymentData['card_last_four'],
            'card_type' => $paymentData['card_type'],
            'expires_at' => now()->addDays(7)
        ]);
    }

    public function processPaymentCapture(Appointment $appointment): bool
    {
        $paymentHold = $this->paymentHoldRepository->findByAppointmentId($appointment->id);

        if (!$paymentHold || $paymentHold->status !== PaymentStatus::AUTHORIZED->value) {
            return false;
        }

        $actualCost = $this->calculateActualCost($appointment);
        $captureResult = $this->capturePayment(
            $paymentHold->cardknox_transaction_id,
            $actualCost,
            'APPOINTMENT_' . $appointment->id
        );

        if ($captureResult['success']) {
            $this->paymentHoldRepository->update($paymentHold->id, [
                'status' => PaymentStatus::CAPTURED->value,
                'amount' => $actualCost,
                'captured_at' => now()
            ]);
            return true;
        }

        return false;
    }

    private function calculateActualCost(Appointment $appointment): float
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
            return $appointment->estimated_cost;
        }

        $hourlyRate = $appointment->serviceType->hourly_rate;
        return ($hourlyRate / 60) * $totalMinutes;
    }
}
