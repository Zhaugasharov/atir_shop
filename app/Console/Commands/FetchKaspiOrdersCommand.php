<?php

namespace App\Console\Commands;

use App\Models\BroadcastMessage;
use App\Models\Order;
use App\Models\WhatsappTemplateSetting;
use App\Services\KaspiApiService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class FetchKaspiOrdersCommand extends Command
{
    protected $signature = 'kaspi:fetch-orders';

    protected $description = 'Fetch new orders from Kaspi API and send WhatsApp notifications';

    public function handle(KaspiApiService $kaspiService, WhatsAppService $whatsappService): int
    {
        $toMs = (int) (microtime(true) * 1000);
        $fromMs = $toMs - 60000; // last minute

        $orders = $kaspiService->getOrdersByDateRange($fromMs, $toMs);

        if (empty($orders)) {
            return Command::SUCCESS;
        }

        $defaultTemplate = WhatsappTemplateSetting::getDefaultTemplate();
        if (!$defaultTemplate) {
            $this->warn('No message template configured. Skipping WhatsApp notifications.');
        }

        foreach ($orders as $orderData) {
            $kaspiId = $orderData['id'] ?? null;
            if (!$kaspiId) {
                continue;
            }

            if (Order::where('order_id', $kaspiId)->exists()) {
                continue;
            }

            $requiredProductId = config('services.kaspi.required_product_id', '143860110');
            if (!$kaspiService->orderContainsProduct($kaspiId, $requiredProductId)) {
                continue;
            }

            $attrs = $orderData['attributes'] ?? [];
            $customer = $attrs['customer'] ?? [];
            $phone = $customer['cellPhone'] ?? '';
            $firstName = $customer['firstName'] ?? '';
            $lastName = $customer['lastName'] ?? '';
            $totalPrice = $attrs['totalPrice'] ?? 0;
            $code = $attrs['code'] ?? $kaspiId;

            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (substr($phone, 0, 1) === '8') {
                $phone = '7' . substr($phone, 1);
            } elseif (substr($phone, 0, 1) !== '7') {
                $phone = '7' . $phone;
            }

            $order = Order::create([
                'order_id' => $kaspiId,
                'phone' => $phone,
                'status' => 1,
                'product_id_1' => null,
                'product_id_2' => null,
                'product_id_3' => null,
            ]);

            if ($defaultTemplate) {
                $bodyParams = [
                    $code,
                    number_format($totalPrice, 0, '', ' '),
                    trim($firstName . ' ' . $lastName),
                    url('/order/' . $kaspiId),
                ];

                $result = $whatsappService->sendTemplateMessage(
                    $phone,
                    $defaultTemplate['name'],
                    $defaultTemplate['language'],
                    $bodyParams
                );
                $wabaMessageId = $result['message_id'];

                $messagePreview = sprintf(
                    'Шаблон %s: заказ %s, %s тг, %s',
                    $defaultTemplate['name'],
                    $code,
                    number_format($totalPrice, 0, '', ' '),
                    trim($firstName . ' ' . $lastName)
                );

                BroadcastMessage::create([
                    'order_id' => $order->id,
                    'phone' => $phone,
                    'message' => $messagePreview,
                    'waba_message_id' => $wabaMessageId,
                    'delivery_status' => BroadcastMessage::STATUS_SENT,
                    'source' => BroadcastMessage::SOURCE_KASPI,
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
