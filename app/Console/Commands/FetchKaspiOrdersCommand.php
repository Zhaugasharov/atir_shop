<?php

namespace App\Console\Commands;

use App\Models\BroadcastMessage;
use App\Models\MessageTemplate;
use App\Models\Order;
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

        $template = MessageTemplate::getDefault();
        if (!$template) {
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

            if ($template) {
                $messageText = $template->render([
                    'order_id' => $code,
                    'order_link' => url('/order/' . $kaspiId),
                    'total_price' => number_format($totalPrice, 0, '', ' '),
                    'customer_name' => trim($firstName . ' ' . $lastName),
                    'phone' => $phone,
                ]);

                $wabaMessageId = $whatsappService->sendMessage($phone, $messageText);

                BroadcastMessage::create([
                    'order_id' => $order->id,
                    'phone' => $phone,
                    'message' => $messageText,
                    'waba_message_id' => $wabaMessageId,
                    'delivery_status' => BroadcastMessage::STATUS_SENT,
                    'source' => BroadcastMessage::SOURCE_KASPI,
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
