<?php

namespace App\Http\Controllers;

use App\Models\BroadcastMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function handle(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->verify($request);
        }

        return $this->receive($request);
    }

    protected function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = config('services.whatsapp.verify_token');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    protected function receive(Request $request)
    {
        $payload = $request->all();

        if (!isset($payload['entry'])) {
            return response()->json(['ok' => true]);
        }

        $whatsappService = app(WhatsAppService::class);
        $ownerPhone = config('services.whatsapp.owner_phone');

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                if (($change['field'] ?? '') !== 'messages') {
                    continue;
                }

                $value = $change['value'] ?? [];
                $statuses = $value['statuses'] ?? [];

                foreach ($statuses as $statusData) {
                    $wabaMessageId = $statusData['id'] ?? null;
                    $status = $statusData['status'] ?? null;

                    if (!$wabaMessageId || !$status) {
                        continue;
                    }

                    $broadcast = BroadcastMessage::where('waba_message_id', $wabaMessageId)->first();

                    if (!$broadcast) {
                        continue;
                    }

                    if ($status === 'delivered') {
                        $broadcast->update(['delivery_status' => BroadcastMessage::STATUS_DELIVERED]);
                    } elseif (in_array($status, ['failed', 'undelivered'])) {
                        $broadcast->update(['delivery_status' => BroadcastMessage::STATUS_FAILED]);

                        if ($ownerPhone) {
                            $orderId = $broadcast->order->order_id ?? $broadcast->order_id;
                            $notificationText = "По заказу {$orderId} (API Kaspi) сообщение не доставлено.";
                            $whatsappService->sendMessage($ownerPhone, $notificationText);
                        }
                    }
                }
            }
        }

        return response()->json(['ok' => true]);
    }
}
