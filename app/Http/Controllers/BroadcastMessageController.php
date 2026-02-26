<?php

namespace App\Http\Controllers;

use App\Models\BroadcastMessage;
use Illuminate\Http\Request;

class BroadcastMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = BroadcastMessage::with('order');

        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->delivery_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('order_id', 'like', "%{$search}%");
                    });
            });
        }

        $broadcastMessages = $query->orderByDesc('created_at')->paginate(20);

        return view('broadcasts.index', [
            'broadcast_messages' => $broadcastMessages,
            'request' => $request,
        ]);
    }
}
