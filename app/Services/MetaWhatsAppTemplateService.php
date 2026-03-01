<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MetaWhatsAppTemplateService
{
    private string $baseUrl = 'https://graph.facebook.com/v18.0';
    private string $wabaId;
    private string $accessToken;

    public function __construct()
    {
        $this->wabaId = config('services.whatsapp.business_account_id', '');
        $this->accessToken = config('services.whatsapp.access_token', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->wabaId) && !empty($this->accessToken);
    }

    /**
     * @return array{data: array, error?: array}
     */
    public function listTemplates(?string $status = null, ?string $language = null): array
    {
        if (!$this->isConfigured()) {
            return ['data' => [], 'error' => ['message' => 'WhatsApp Business Account ID not configured']];
        }

        $params = [];
        if ($status) {
            $params['status'] = $status;
        }
        if ($language) {
            $params['language'] = $language;
        }

        $url = "{$this->baseUrl}/{$this->wabaId}/message_templates";
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $result = $this->request('GET', $url);

        if (isset($result['error'])) {
            return ['data' => [], 'error' => $result['error']];
        }

        $httpCode = $result['http_code'];
        $body = $result['body'];

        if ($httpCode < 200 || $httpCode >= 300) {
            Log::error('Meta WhatsApp listTemplates error', [
                'status' => $httpCode,
                'body' => $body,
            ]);
            return ['data' => [], 'error' => is_array($body) ? $body : ['message' => $body]];
        }

        return is_array($body) ? $body : ['data' => []];
    }

    /**
     * @return array{id?: string, status?: string, category?: string, error?: array}
     */
    public function createTemplate(array $payload): array
    {
        if (!$this->isConfigured()) {
            return ['error' => ['message' => 'WhatsApp Business Account ID not configured']];
        }

        $url = "{$this->baseUrl}/{$this->wabaId}/message_templates";

        $result = $this->request('POST', $url, $payload);

        if (isset($result['error'])) {
            return ['error' => $result['error']];
        }

        $httpCode = $result['http_code'];
        $body = $result['body'];

        if ($httpCode < 200 || $httpCode >= 300) {
            Log::error('Meta WhatsApp createTemplate error', [
                'status' => $httpCode,
                'body' => $body,
            ]);
            return ['error' => is_array($body) ? $body : ['message' => $body]];
        }

        return is_array($body) ? $body : [];
    }

    /**
     * @return array{success?: bool, error?: array}
     */
    public function deleteTemplate(string $name): array
    {
        if (!$this->isConfigured()) {
            return ['error' => ['message' => 'WhatsApp Business Account ID not configured']];
        }

        $url = "{$this->baseUrl}/{$this->wabaId}/message_templates?name=" . urlencode($name);

        $result = $this->request('DELETE', $url);

        if (isset($result['error'])) {
            return ['error' => $result['error']];
        }

        $httpCode = $result['http_code'];
        $body = $result['body'];

        if ($httpCode < 200 || $httpCode >= 300) {
            Log::error('Meta WhatsApp deleteTemplate error', [
                'status' => $httpCode,
                'body' => $body,
            ]);
            return ['error' => is_array($body) ? $body : ['message' => $body]];
        }

        return is_array($body) ? $body : ['success' => true];
    }

    /**
     * @return array{http_code: int, body: array|string, error?: string}
     */
    private function request(string $method, string $url, ?array $postData = null): array
    {
        $ch = curl_init($url);
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
        ];

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($method === 'GET') {
            $options[CURLOPT_HTTPGET] = true;
        } elseif ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($postData ?? []);
        } elseif ($method === 'DELETE') {
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::error('Meta WhatsApp API cURL error: ' . $curlError);
            return ['http_code' => 0, 'body' => [], 'error' => $curlError];
        }

        $body = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $body = $response;
        }

        return ['http_code' => $httpCode, 'body' => $body];
    }
}
