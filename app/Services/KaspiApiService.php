<?php

namespace App\Services;

use App\Models\KaspiServiceLog;
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
            KaspiServiceLog::create([
                'status' => KaspiServiceLog::STATUS_WARNING,
                'message' => 'KASPI_AUTH_TOKEN not configured',
            ]);
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
                $errorMessage = sprintf('HTTP %d: %s', $response->status(), $response->body());
                Log::error('Kaspi API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                KaspiServiceLog::create([
                    'status' => KaspiServiceLog::STATUS_ERROR,
                    'message' => $errorMessage,
                ]);
                return [];
            }

            $data = $response->json();
            $orders = $data['data'] ?? [];
            KaspiServiceLog::create([
                'status' => KaspiServiceLog::STATUS_SUCCESS,
                'message' => count($orders) > 0 ? sprintf('OK, заказов: %d', count($orders)) : 'OK',
            ]);
            return $orders;
        } catch (\Exception $e) {
            Log::error('Kaspi API exception: ' . $e->getMessage());
            KaspiServiceLog::create([
                'status' => KaspiServiceLog::STATUS_ERROR,
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Получить позиции заказа (entries) с товарами.
     * Каждая позиция содержит relationships.product.data.id — ID товара в Kaspi.
     */
    public function getOrderEntries(string $orderId): array
    {
        $token = config('services.kaspi.token');
        $baseUrl = rtrim(config('services.kaspi.api_url'), '/');

        if (empty($token)) {
            return [];
        }

        $url = $baseUrl . '/' . $orderId . '/entries';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/vnd.api+json',
                'X-Auth-Token' => $token,
            ])->get($url);

            if (!$response->successful()) {
                return [];
            }

            $data = $response->json();
            return $data['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Kaspi API getOrderEntries exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Проверить, содержит ли заказ товар с указанным ID.
     */
    public function orderContainsProduct(string $orderId, string $productId): bool
    {
        $entries = $this->getOrderEntries($orderId);

        foreach ($entries as $entry) {
            $productData = $entry['relationships']['product']['data'] ?? null;
            if (!$productData) {
                continue;
            }
            $entryProductId = $productData['id'] ?? null;
            if ($entryProductId !== null && (string) $entryProductId === (string) $productId) {
                return true;
            }
        }

        return false;
    }
}
