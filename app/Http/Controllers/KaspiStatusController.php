<?php

namespace App\Http\Controllers;

use App\Models\KaspiServiceLog;
use App\Services\KaspiApiService;

class KaspiStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $lastLog = KaspiServiceLog::orderByDesc('created_at')->first();
        $recentErrors = KaspiServiceLog::latestErrors(20)->get();

        return view('kaspi-status.index', [
            'lastLog' => $lastLog,
            'recentErrors' => $recentErrors,
        ]);
    }

    public function test(KaspiApiService $kaspiService)
    {
        $toMs = (int) (microtime(true) * 1000);
        $fromMs = $toMs - 86400000; // последние 24 часа

        $orders = $kaspiService->getOrdersByDateRange($fromMs, $toMs);

        $lastLog = KaspiServiceLog::orderByDesc('created_at')->first();
        $count = count($orders);

        if ($lastLog && $lastLog->status === KaspiServiceLog::STATUS_ERROR) {
            return redirect()->route('kaspi-status.index')
                ->with('error', 'Тестовый запрос завершился с ошибкой. См. лог ниже.');
        }

        if ($count > 0) {
            return redirect()->route('kaspi-status.index')
                ->with('success', "Тестовый запрос выполнен. Найдено заказов: {$count}");
        }

        return redirect()->route('kaspi-status.index')
            ->with('success', 'Тестовый запрос выполнен. Заказов за последние 24 часа не найдено.');
    }
}
