<?php

namespace App\Http\Controllers;

use App\Models\WhatsappTemplateSetting;
use App\Services\MetaWhatsAppTemplateService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(MetaWhatsAppTemplateService $metaService)
    {
        $result = $metaService->listTemplates();
        $templates = $result['data'] ?? [];
        $error = $result['error'] ?? null;

        $defaultTemplate = WhatsappTemplateSetting::getDefaultTemplate();

        return view('message-templates.index', [
            'templates' => $templates,
            'defaultTemplate' => $defaultTemplate,
            'apiError' => $error,
            'metaConfigured' => $metaService->isConfigured(),
        ]);
    }

    public function store(Request $request, MetaWhatsAppTemplateService $metaService)
    {
        $request->validate([
            'name' => 'required|string|max:512|regex:/^[a-z0-9_]+$/i',
            'language' => 'required|string|size:2',
            'category' => 'required|in:UTILITY,MARKETING,AUTHENTICATION',
            'body' => 'required|string',
            'body_example' => 'required|string',
            'is_default' => 'nullable|boolean',
            'edit_name' => 'nullable|string',
            'edit_language' => 'nullable|string',
        ]);

        $editName = $request->edit_name;
        $editLanguage = $request->edit_language;

        if ($editName && $editLanguage) {
            $deleteResult = $metaService->deleteTemplate($editName);
            if (!empty($deleteResult['error'])) {
                $errMsg = $deleteResult['error']['error']['message'] ?? json_encode($deleteResult['error']);
                return redirect()->route('message-templates.index')
                    ->with('error', 'Не удалось удалить старый шаблон: ' . $errMsg);
            }
        }

        $exampleParts = array_map('trim', explode('|', $request->body_example));
        $exampleParts = array_filter($exampleParts);

        $components = [
            [
                'type' => 'BODY',
                'text' => $request->body,
                'example' => [
                    'body_text' => [$exampleParts],
                ],
            ],
        ];

        $payload = [
            'name' => $request->name,
            'language' => $request->language,
            'category' => $request->category,
            'components' => $components,
        ];

        $result = $metaService->createTemplate($payload);

        if (!empty($result['error'])) {
            $errMsg = $result['error']['error']['message'] ?? json_encode($result['error']);
            return redirect()->route('message-templates.index')
                ->with('error', 'Не удалось создать шаблон: ' . $errMsg);
        }

        if ($request->boolean('is_default')) {
            WhatsappTemplateSetting::setDefaultTemplate($request->name, $request->language);
        }

        return redirect()->route('message-templates.index')
            ->with('success', 'Шаблон создан. Статус: ' . ($result['status'] ?? 'PENDING') . '. Ожидайте модерации Meta.');
    }

    public function setDefault(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:512',
            'language' => 'required|string|max:10',
        ]);

        WhatsappTemplateSetting::setDefaultTemplate($request->name, $request->language);

        return redirect()->route('message-templates.index')->with('success', 'Шаблон установлен по умолчанию');
    }

    public function destroy(string $name, MetaWhatsAppTemplateService $metaService)
    {
        $result = $metaService->deleteTemplate($name);

        if (!empty($result['error'])) {
            $errMsg = $result['error']['error']['message'] ?? json_encode($result['error']);
            return redirect()->route('message-templates.index')
                ->with('error', 'Не удалось удалить шаблон: ' . $errMsg);
        }

        $default = WhatsappTemplateSetting::getDefaultTemplate();
        if ($default && $default['name'] === $name) {
            WhatsappTemplateSetting::set(WhatsappTemplateSetting::KEY_DEFAULT_TEMPLATE_NAME, null);
        }

        return redirect()->route('message-templates.index')->with('success', 'Шаблон удален');
    }

    public function sendTest(Request $request, WhatsAppService $whatsappService)
    {
        $request->validate([
            'template_name' => 'required|string|max:512',
            'template_language' => 'required|string|max:10',
            'phone' => 'required|string|min:10',
            'body_params' => 'nullable|string',
        ]);

        $bodyParams = [];
        if (!empty($request->body_params)) {
            $bodyParams = array_map('trim', explode('|', $request->body_params));
            $bodyParams = array_filter($bodyParams);
        }

        $result = $whatsappService->sendTemplateMessage(
            $request->phone,
            $request->template_name,
            $request->template_language,
            $bodyParams
        );

        if ($result['message_id']) {
            return redirect()->route('message-templates.index')
                ->with('success', 'Тестовое сообщение отправлено на ' . $request->phone);
        }

        $errMsg = $result['error']['message'] ?? 'Unknown error';
        if (!empty($result['error']['details'])) {
            $errMsg .= ' — ' . $result['error']['details'];
        }

        return redirect()->route('message-templates.index')
            ->with('error', 'Не удалось отправить сообщение: ' . $errMsg);
    }
}
