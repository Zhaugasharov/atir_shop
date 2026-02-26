<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $templates = MessageTemplate::orderBy('name')->get();
        return view('message-templates.index', ['templates' => $templates]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'required|string',
            'is_default' => 'nullable|boolean',
        ]);

        $template = MessageTemplate::find($request->template_id);

        if ($template) {
            $template->update([
                'name' => $request->name,
                'body' => $request->body,
                'is_default' => $request->boolean('is_default'),
            ]);
            MessageTemplate::where('id', '!=', $template->id)->update(['is_default' => false]);
            return redirect()->route('message-templates.index')->with('success', 'Шаблон обновлен');
        }

        $template = MessageTemplate::create([
            'name' => $request->name,
            'body' => $request->body,
            'is_default' => $request->boolean('is_default'),
        ]);
        if ($request->boolean('is_default')) {
            MessageTemplate::where('id', '!=', $template->id)->update(['is_default' => false]);
        }
        return redirect()->route('message-templates.index')->with('success', 'Шаблон создан');
    }

    public function setDefault($id)
    {
        MessageTemplate::query()->update(['is_default' => false]);
        $template = MessageTemplate::findOrFail($id);
        $template->update(['is_default' => true]);
        return redirect()->route('message-templates.index')->with('success', 'Шаблон установлен по умолчанию');
    }

    public function sendTest(Request $request, WhatsAppService $whatsappService)
    {
        $request->validate([
            'template_id' => 'required|exists:message_templates,id',
            'phone' => 'required|string|min:10',
        ]);

        $template = MessageTemplate::findOrFail($request->template_id);

        $testData = [
            'order_id' => 'TEST-123',
            'order_link' => url('/order/TEST-123'),
            'total_price' => '50 000',
            'customer_name' => 'Тестовый клиент',
            'phone' => $request->phone,
        ];

        $messageText = $template->render($testData);
        $wabaMessageId = $whatsappService->sendMessage($request->phone, $messageText);

        if ($wabaMessageId) {
            return redirect()->route('message-templates.index')->with('success', 'Тестовое сообщение отправлено на ' . $request->phone);
        }

        return redirect()->route('message-templates.index')->with('error', 'Не удалось отправить сообщение. Проверьте настройки WhatsApp в .env');
    }
}
