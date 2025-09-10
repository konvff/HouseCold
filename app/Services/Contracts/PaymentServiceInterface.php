<?php

namespace App\Services\Contracts;

use App\Models\Appointment;
use App\Models\PaymentHold;

interface PaymentServiceInterface
{
    public function authorizePayment(array $paymentData, array $cardData, array $customerInfo): array;
    public function capturePayment(string $transactionId, float $amount, string $description): array;
    public function voidTransaction(string $transactionId): array;
    public function refundPayment(string $transactionId, float $amount): array;
    public function createPaymentHold(Appointment $appointment, array $paymentData): PaymentHold;
    public function processPaymentCapture(Appointment $appointment): bool;
}
