<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\PaymentHold;
use Illuminate\Http\Request;
use App\Services\CardknoxPaymentService;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount' => 'required|numeric|min:0',
            'card_data' => 'required|array',
            'card_data.card_number' => 'required|string',
            'card_data.expiry_month' => 'required|string',
            'card_data.expiry_year' => 'required|string',
            'card_data.cvv' => 'required|string',
            'customer_info' => 'required|array',
            'customer_info.first_name' => 'required|string',
            'customer_info.last_name' => 'required|string',
            'customer_info.email' => 'required|email',
            'customer_info.phone' => 'required|string',
            'customer_info.address' => 'required|string',
            'customer_info.city' => 'required|string',
            'customer_info.state' => 'required|string',
            'customer_info.zip' => 'required|string'
        ]);

        try {
            $appointment = Appointment::findOrFail($request->appointment_id);

            // Use Cardknox to authorize payment (hold funds)
            $cardknoxService = new CardknoxPaymentService();
            $authResult = $cardknoxService->authorizePayment(
                $request->amount,
                $request->card_data,
                $request->customer_info,
                'APPOINTMENT_' . $request->appointment_id
            );

            if ($authResult['success']) {
                // Create or update payment hold record
                $paymentHold = PaymentHold::updateOrCreate(
                    ['appointment_id' => $request->appointment_id],
                    [
                        'amount' => $request->amount,
                        'status' => 'authorized',
                        'cardknox_transaction_id' => $authResult['transaction_id'],
                        'cardknox_auth_code' => $authResult['auth_code'],
                        'card_last_four' => substr($request->card_data['card_number'], -4),
                        'card_type' => $this->detectCardType($request->card_data['card_number']),
                        'expires_at' => now()->addDays(7) // Authorization hold expires in 7 days
                    ]
                );

                return response()->json([
                    'success' => true,
                    'transaction_id' => $authResult['transaction_id'],
                    'auth_code' => $authResult['auth_code'],
                    'message' => 'Payment authorized successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $authResult['error'],
                    'error_code' => $authResult['error_code']
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'appointment_id' => 'required|exists:appointments,id'
        ]);

        try {
            $appointment = Appointment::findOrFail($request->appointment_id);

            // Check if payment hold exists and is authorized
            $paymentHold = $appointment->paymentHold;
            if ($paymentHold && $paymentHold->status === 'authorized') {
                return response()->json(['success' => true, 'message' => 'Payment already authorized']);
            }

            return response()->json(['error' => 'Payment not found or not authorized'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function capturePayment(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id'
        ]);

        try {
            $appointment = Appointment::with('paymentHold')->findOrFail($request->appointment_id);

            if (!$appointment->paymentHold || $appointment->paymentHold->status !== 'authorized') {
                return response()->json(['error' => 'No authorized payment found'], 400);
            }

            // Prevent double capture
            if ($appointment->paymentHold->status === 'captured') {
                return response()->json(['error' => 'Payment has already been captured'], 400);
            }

            // Calculate actual cost based on time worked, or use estimated cost if no time logs
            $actualCost = $appointment->actual_cost ?: $appointment->estimated_cost;
            $chargeAmount = min($actualCost, $appointment->paymentHold->amount); // Don't exceed authorized amount

            // Use Cardknox to capture the payment
            $cardknoxService = new CardknoxPaymentService();
            $captureResult = $cardknoxService->capturePayment(
                $appointment->paymentHold->cardknox_transaction_id,
                $chargeAmount,
                'APPOINTMENT_' . $request->appointment_id
            );

            if ($captureResult['success']) {
                $appointment->paymentHold->update([
                    'status' => 'captured',
                    'amount' => $chargeAmount,
                    'captured_at' => now()
                ]);
                return response()->json(['success' => true, 'message' => 'Payment captured successfully! Amount: $' . number_format($chargeAmount, 2)]);
            } else {
                return response()->json(['error' => $captureResult['error']], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function refundPayment(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount' => 'nullable|numeric|min:0'
        ]);

        try {
            $appointment = Appointment::with('paymentHold')->findOrFail($request->appointment_id);

            if (!$appointment->paymentHold || $appointment->paymentHold->status !== 'captured') {
                return response()->json(['error' => 'No captured payment found'], 400);
            }

            // Use Cardknox to process refund
            $cardknoxService = new CardknoxPaymentService();
            $refundAmount = $request->amount ?: $appointment->paymentHold->amount;

            $refundResult = $cardknoxService->refundPayment(
                $appointment->paymentHold->cardknox_transaction_id,
                $refundAmount
            );

            if ($refundResult['success']) {
                return response()->json(['success' => true, 'message' => 'Refund processed successfully']);
            } else {
                return response()->json(['error' => $refundResult['error']], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getPaymentStatus(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id'
        ]);

        $appointment = Appointment::with('paymentHold')->findOrFail($request->appointment_id);

        return response()->json([
            'payment_status' => $appointment->paymentHold ? $appointment->paymentHold->status : 'none',
            'amount' => $appointment->paymentHold ? $appointment->paymentHold->amount : 0,
            'estimated_cost' => $appointment->estimated_cost,
            'actual_cost' => $appointment->actual_cost
        ]);
    }

    /**
     * Void a transaction (cancel authorization before capture)
     */
    public function voidTransaction(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id'
        ]);

        try {
            $appointment = Appointment::with('paymentHold')->findOrFail($request->appointment_id);

            if (!$appointment->paymentHold || $appointment->paymentHold->status !== 'authorized') {
                return response()->json(['error' => 'No authorized payment found'], 400);
            }

            // Use Cardknox to void the transaction
            $cardknoxService = new CardknoxPaymentService();
            $voidResult = $cardknoxService->voidTransaction(
                $appointment->paymentHold->cardknox_transaction_id
            );

            if ($voidResult['success']) {
                $appointment->paymentHold->update(['status' => 'voided']);
                return response()->json(['success' => true, 'message' => 'Transaction voided successfully']);
            } else {
                return response()->json(['error' => $voidResult['error']], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Detect card type based on card number
     */
    private function detectCardType($cardNumber)
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        if (preg_match('/^4/', $cardNumber)) {
            return 'visa';
        } elseif (preg_match('/^5[1-5]/', $cardNumber)) {
            return 'mastercard';
        } elseif (preg_match('/^3[47]/', $cardNumber)) {
            return 'amex';
        } elseif (preg_match('/^6/', $cardNumber)) {
            return 'discover';
        } else {
            return 'unknown';
        }
    }
}
