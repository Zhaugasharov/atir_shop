<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KaspiApiService
{
    public function getOrdersByDateRange(int $fromMs, int $toMs): array
    {
        $token = config('services.kaspi.token');
        $apiUrl = config('services.kaspi.api_url');

        if (empty($token)) {
            Log::warning('Kaspi API: KASPI_AUTH_TOKEN not configured');
            return [];
        }

        $params = [
            'page[number]' => 0,
            'page[size]' => 100,
            'filter[orders][state]' => 'NEW',
            'filter[orders][creationDate][$ge]' => $fromMs,
            'filter[orders][creationDate][$le]' => $toMs,
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/vnd.api+json',
                'X-Auth-Token' => $token,
            ])->get($apiUrl, $params);

            if (!$response->successful()) {
                Log::error('Kaspi API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $data = $response->json();
            return $data['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Kaspi API exception: ' . $e->getMessage());
            return [];
        }
    }
}
