<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CardknoxPaymentService
{
    protected $baseUrl;
    protected $key;
    protected $version;

    public function __construct()
    {
        $this->baseUrl = config('services.cardknox.base_url', 'https://x1.cardknox.com/gateway');
        $this->key = config('services.cardknox.key');
        $this->version = config('services.cardknox.version', '4.5.9');



        if (!$this->key) {
            \Log::error('Cardknox API key not configured');
            throw new \Exception('Cardknox API key not configured');
        }
    }

    /**
     * Authorize a payment (hold funds)
     */
    public function authorizePayment($amount, $cardData, $customerInfo, $orderId = null)
    {
        $payload = [
            'xKey' => $this->key,
            'xCommand' => 'cc:authonly',
            'xAmount' => number_format($amount, 2, '.', ''),
            'xCurrency' => 'USD',
            'xCardNum' => $cardData['card_number'],
            'xExp' => $cardData['expiry_month'] . substr($cardData['expiry_year'], -2),
            'xCVV' => $cardData['cvv'],
            'xInvoice' => $orderId ?? uniqid('ORDER_'),
            'xVersion' => $this->version,
            'xSoftwareName' => 'House Call Scheduler',
            'xSoftwareVersion' => '1.0.0',
        ];

        // Log the payload being sent to Cardknox (without sensitive data)
        $logPayload = $payload;
        $logPayload['xCardNum'] = '****' . substr($cardData['card_number'], -4);
        $logPayload['xCVV'] = '***';
        \Log::info('Cardknox authorization payload: ', $logPayload);
        \Log::info('Cardknox key being used: ' . substr($this->key, 0, 10) . '...');

                try {
            // Use form data as Cardknox expects
            $response = Http::asForm()->post($this->baseUrl, $payload);

            \Log::info('Cardknox HTTP response status: ' . $response->status());
            \Log::info('Cardknox HTTP response body: ' . $response->body());

            if ($response->successful()) {
                $result = $this->parseResponse($response->body());

                // Check if we have the required fields
                if (!isset($result['xResult'])) {
                    \Log::error('Cardknox response missing xResult field', ['result' => $result]);
                    return [
                        'success' => false,
                        'error' => 'Invalid response from payment gateway',
                        'error_code' => 'INVALID_RESPONSE'
                    ];
                }

                if ($result['xResult'] === 'A') {
                    return [
                        'success' => true,
                        'transaction_id' => $result['xRefNum'] ?? 'unknown',
                        'auth_code' => $result['xAuthCode'] ?? 'unknown',
                        'message' => 'Authorization successful'
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => $result['xError'] ?? 'Authorization failed',
                        'error_code' => $result['xResult']
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Network error',
                'error_code' => 'NETWORK_ERROR'
            ];

        } catch (\Exception $e) {
            Log::error('Cardknox authorization error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service temporarily unavailable',
                'error_code' => 'SERVICE_ERROR'
            ];
        }
    }

    /**
     * Capture a previously authorized payment
     */
    public function capturePayment($transactionId, $amount, $orderId = null)
    {
        $payload = [
            'xKey' => $this->key,
            'xCommand' => 'cc:capture',
            'xRefNum' => $transactionId,
            'xAmount' => number_format($amount, 2, '.', ''),
            'xInvoice' => $orderId ?? uniqid('CAPTURE_'),
            'xVersion' => $this->version,
            'xSoftwareName' => 'House Call Scheduler',
            'xSoftwareVersion' => '1.0.0',
        ];

        try {
            $response = Http::asForm()->post($this->baseUrl, $payload);

            if ($response->successful()) {
                $result = $this->parseResponse($response->body());

                if ($result['xResult'] === 'A') {
                    return [
                        'success' => true,
                        'transaction_id' => $result['xRefNum'],
                        'capture_id' => $result['xAuthCode'],
                        'message' => 'Payment captured successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => $result['xError'] ?? 'Capture failed',
                        'error_code' => $result['xResult']
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Network error',
                'error_code' => 'NETWORK_ERROR'
            ];

        } catch (\Exception $e) {
            Log::error('Cardknox capture error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service temporarily unavailable',
                'error_code' => 'SERVICE_ERROR'
            ];
        }
    }

    /**
     * Void a transaction (cancel authorization before capture)
     */
    public function voidTransaction($transactionId)
    {
        $payload = [
            'xKey' => $this->key,
            'xCommand' => 'cc:void',
            'xRefNum' => $transactionId,
            'xVersion' => $this->version,
            'xSoftwareName' => 'House Call Scheduler',
            'xSoftwareVersion' => '1.0.0',
        ];

        try {
            $response = Http::asForm()->post($this->baseUrl, $payload);

            if ($response->successful()) {
                $result = $this->parseResponse($response->body());

                if ($result['xResult'] === 'A') {
                    return [
                        'success' => true,
                        'transaction_id' => $result['xRefNum'],
                        'message' => 'Transaction voided successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => $result['xError'] ?? 'Void failed',
                        'error_code' => $result['xResult']
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Network error',
                'error_code' => 'NETWORK_ERROR'
            ];

        } catch (\Exception $e) {
            Log::error('Cardknox void error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service temporarily unavailable',
                'error_code' => 'SERVICE_ERROR'
            ];
        }
    }

    /**
     * Refund a captured payment
     */
    public function refundPayment($transactionId, $amount)
    {
        $payload = [
            'xKey' => $this->key,
            'xCommand' => 'cc:refund',
            'xRefNum' => $transactionId,
            'xAmount' => number_format($amount, 2, '.', ''),
            'xVersion' => $this->version,
            'xSoftwareName' => 'House Call Scheduler',
            'xSoftwareVersion' => '1.0.0',
        ];

        try {
            $response = Http::asForm()->post($this->baseUrl, $payload);

            if ($response->successful()) {
                $result = $this->parseResponse($response->body());

                if ($result['xResult'] === 'A') {
                    return [
                        'success' => true,
                        'transaction_id' => $result['xRefNum'],
                        'refund_id' => $result['xAuthCode'],
                        'message' => 'Refund processed successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => $result['xError'] ?? 'Refund failed',
                        'error_code' => $result['xResult']
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Network error',
                'error_code' => 'NETWORK_ERROR'
            ];

        } catch (\Exception $e) {
            Log::error('Cardknox refund error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service temporarily unavailable',
                'error_code' => 'SERVICE_ERROR'
            ];
        }
    }

                /**
     * Parse Cardknox response
     */
    protected function parseResponse($response)
    {
        // Log the raw response for debugging
        \Log::info('Cardknox raw response: ' . $response);

        $result = [];

        // Try to parse as JSON first
        $jsonResult = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $result = $jsonResult;
        } else {
            // Fallback to key-value parsing
            if (strpos($response, '&') !== false) {
                // Parse & separated key-value pairs
                $pairs = explode('&', $response);
                foreach ($pairs as $pair) {
                    if (strpos($pair, '=') !== false) {
                        list($key, $value) = explode('=', $pair, 2);
                        $result[trim($key)] = urldecode(trim($value));
                    }
                }
            } else {
                // Fallback to line-based parsing
                $lines = explode("\n", $response);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (strpos($line, '=') !== false) {
                        list($key, $value) = explode('=', $line, 2);
                        $result[trim($key)] = urldecode(trim($value));
                    }
                }
            }
        }

        // Log the parsed result for debugging
        \Log::info('Cardknox parsed response: ', $result);

        return $result;
    }
}
