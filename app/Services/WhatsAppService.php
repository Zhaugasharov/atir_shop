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

    /**
     * Send a template message via WhatsApp Cloud API.
     *
     * @param string $phone Recipient phone (will be normalized)
     * @param string $templateName Meta template name
     * @param string $language Language code (e.g. ru, en, kk)
     * @param array<string> $bodyParams Parameters for {{1}}, {{2}}, etc. in body
     * @return array{message_id: ?string, error: ?array}
     */
    public function sendTemplateMessage(string $phone, string $templateName, string $language, array $bodyParams = []): array
    {
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $accessToken = config('services.whatsapp.access_token');

        if (empty($phoneNumberId) || empty($accessToken)) {
            Log::warning('WhatsApp: phone_number_id or access_token not configured');
            return ['message_id' => null, 'error' => ['message' => 'WhatsApp not configured']];
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) === '8') {
            $phone = '7' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) !== '7') {
            $phone = '7' . $phone;
        }

        $components = [];
        if (!empty($bodyParams)) {
            $parameters = array_map(fn ($text) => ['type' => 'text', 'text' => (string) $text], $bodyParams);
            $components[] = [
                'type' => 'body',
                'parameters' => $parameters,
            ];
        }

        $template = [
            'name' => $templateName,
            'language' => ['code' => $language],
        ];
        if (!empty($components)) {
            $template['components'] = $components;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => $template,
        ];

        $url = "https://graph.facebook.com/v18.0/{$phoneNumberId}/messages";

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
            return ['message_id' => null, 'error' => ['message' => $curlError]];
        }

        $data = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            Log::error('WhatsApp API template error', [
                'status' => $httpCode,
                'body' => $response,
                'payload' => $payload,
            ]);
            $error = $data['error'] ?? ['message' => $response];
            $details = $error['error_data']['details'] ?? null;
            if ($details) {
                $error['details'] = $details;
            }
            return ['message_id' => null, 'error' => $error];
        }

        return [
            'message_id' => $data['messages'][0]['id'] ?? null,
            'error' => null,
        ];
    }
}
