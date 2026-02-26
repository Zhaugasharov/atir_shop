<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendMessage(string $phone, string $text): ?string
    {
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $accessToken = config('services.whatsapp.access_token');

        if (empty($phoneNumberId) || empty($accessToken)) {
            Log::warning('WhatsApp: phone_number_id or access_token not configured');
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) === '8') {
            $phone = '7' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) !== '7') {
            $phone = '7' . $phone;
        }

        $url = "https://graph.facebook.com/v18.0/{$phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phone,
            'type' => 'text',
            'text' => [
                'body' => $text,
            ],
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::error('WhatsApp API cURL error: ' . $curlError);
            return null;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            Log::error('WhatsApp API error', [
                'status' => $httpCode,
                'body' => $response,
            ]);
            return null;
        }

        $data = json_decode($response, true);
        return $data['messages'][0]['id'] ?? null;
    }
}
