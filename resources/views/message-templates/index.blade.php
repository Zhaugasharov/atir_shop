@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            @include('partials.admin-sidebar', ['active' => 'templates'])
                        </div>
                        <div class="col-md-9">
                            <h4>Шаблоны сообщений</h4>
                            <a href="https://business.facebook.com/latest/whatsapp_manager/message_templates" target="_blank" rel="noopener" class="btn btn-link btn-sm mb-2">Управление в Meta</a>
                            <hr class="my-4" style="border-top:1px solid #e0e0e0;">

                            @if($apiError ?? null)
                                <div class="alert alert-warning">
                                    Не удалось загрузить шаблоны: {{ is_array($apiError) ? ($apiError['error']['message'] ?? json_encode($apiError)) : $apiError }}.
                                    Проверьте WHATSAPP_BUSINESS_ACCOUNT_ID и WHATSAPP_ACCESS_TOKEN в .env
                                </div>
                            @endif

                            @if(!($metaConfigured ?? true))
                                <div class="alert alert-warning">Укажите WHATSAPP_BUSINESS_ACCOUNT_ID в .env для работы с шаблонами.</div>
                            @endif

                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#templateModal" id="addTemplateBtn">
                                Добавить шаблон
                            </button>
                            <button class="btn btn-outline-success mb-3" data-toggle="modal" data-target="#sendTestModal" id="sendTestBtn" @if(empty($templates)) disabled @endif>
                                Отправить тестовое сообщение
                            </button>

                            <div class="modal fade" id="templateModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <form id="templateForm" action="{{ route('message-templates.store') }}" method="POST" class="modal-content">
                                        @csrf
                                        <input type="hidden" name="edit_name" id="editName">
                                        <input type="hidden" name="edit_language" id="editLanguage">

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="templateModalTitle">Добавить шаблон</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Название (латиница, цифры, подчёркивание)</label>
                                                <input type="text" name="name" class="form-control" required id="templateName" value="{{ old('name') }}" placeholder="order_notification" pattern="[a-zA-Z0-9_]+">
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Язык</label>
                                                    <select name="language" class="form-control" required id="templateLanguage">
                                                        <option value="ru" {{ old('language') == 'ru' ? 'selected' : '' }}>ru</option>
                                                        <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>en</option>
                                                        <option value="kk" {{ old('language') == 'kk' ? 'selected' : '' }}>kk</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Категория</label>
                                                    <select name="category" class="form-control" required>
                                                        <option value="UTILITY" {{ old('category') == 'UTILITY' ? 'selected' : '' }}>UTILITY</option>
                                                        <option value="MARKETING" {{ old('category') == 'MARKETING' ? 'selected' : '' }}>MARKETING</option>
                                                        <option value="AUTHENTICATION" {{ old('category') == 'AUTHENTICATION' ? 'selected' : '' }}>AUTHENTICATION</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Текст сообщения (переменные: {{1}}, {{2}}, {{3}}...)</label>
                                                <textarea name="body" class="form-control" rows="5" required id="templateBody" placeholder="Здравствуйте {{1}}! Ваш заказ {{2}} на сумму {{3}} тг принят.">{{ old('body') }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Примеры для переменных (через |)</label>
                                                <input type="text" name="body_example" class="form-control" required id="templateBodyExample" value="{{ old('body_example', 'Тестовый клиент|TEST-123|50 000') }}" placeholder="Иван|12345|50 000">
                                                <small class="form-text text-muted">Порядок: {{1}}|{{2}}|{{3}}...</small>
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" name="is_default" value="1" id="templateIsDefault">
                                                    <label class="custom-control-label" for="templateIsDefault">Использовать по умолчанию</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                            <button type="submit" class="btn btn-success">Сохранить</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="modal fade" id="sendTestModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <form action="{{ route('message-templates.send-test') }}" method="POST" class="modal-content">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Отправить тестовое сообщение</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Шаблон</label>
                                                <select name="template_name" class="form-control" required id="sendTestTemplateName">
                                                    <option value="">Выберите шаблон</option>
                                                    @foreach($templates ?? [] as $t)
                                                        @php
                                                            $name = $t['name'] ?? '';
                                                            $lang = $t['language'] ?? '';
                                                            $status = $t['status'] ?? '';
                                                            $bodyText = '';
                                                            foreach ($t['components'] ?? [] as $c) {
                                                                if (in_array($c['type'] ?? '', ['BODY', 'body']) && isset($c['text'])) {
                                                                    $bodyText = $c['text'];
                                                                    break;
                                                                }
                                                            }
                                                            $paramCount = 0;
                                                            if (preg_match_all('/\{\{(\d+)\}\}/', $bodyText, $m) && !empty($m[1])) {
                                                                $paramCount = max(array_map('intval', $m[1]));
                                                            }
                                                            $defaultParams = $paramCount > 0 ? implode('|', array_map(fn($i) => 'value' . ($i + 1), range(0, $paramCount - 1))) : '';
                                                        @endphp
                                                        <option value="{{ $name }}" data-language="{{ $lang }}" data-default-params="{{ $defaultParams }}">{{ $name }} ({{ $lang }}) - {{ $status }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="template_language" id="sendTestTemplateLanguage">
                                            </div>
                                            <div class="form-group">
                                                <label>Номер телефона</label>
                                                <input type="text" name="phone" class="form-control" required
                                                       placeholder="77001234567 или 8 700 123 45 67"
                                                       value="{{ old('phone') }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Параметры body (через |)</label>
                                                <input type="text" name="body_params" class="form-control" id="sendTestBodyParams"
                                                       placeholder="value1|value2"
                                                       value="">
                                                <small class="form-text text-muted">Количество параметров должно совпадать с {{1}}, {{2}}... в шаблоне. Разделитель — |</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                            <button type="submit" class="btn btn-success">Отправить</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Название</th>
                                        <th>Язык</th>
                                        <th>Категория</th>
                                        <th>Статус</th>
                                        <th>Текст</th>
                                        <th>По умолчанию</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templates ?? [] as $t)
                                        @php
                                            $name = $t['name'] ?? '';
                                            $lang = $t['language'] ?? '';
                                            $status = $t['status'] ?? 'N/A';
                                            $category = $t['category'] ?? '';
                                            $bodyText = '';
                                            foreach ($t['components'] ?? [] as $c) {
                                                if (($c['type'] ?? '') === 'BODY' && isset($c['text'])) {
                                                    $bodyText = $c['text'];
                                                    break;
                                                }
                                            }
                                            $isDefault = ($defaultTemplate['name'] ?? '') === $name && ($defaultTemplate['language'] ?? '') === $lang;
                                        @endphp
                                        <tr>
                                            <td>{{ $name }}</td>
                                            <td>{{ $lang }}</td>
                                            <td>{{ $category }}</td>
                                            <td>
                                                @if($status === 'APPROVED')
                                                    <span class="badge badge-success">APPROVED</span>
                                                @elseif($status === 'PENDING')
                                                    <span class="badge badge-warning">PENDING</span>
                                                @elseif($status === 'REJECTED')
                                                    <span class="badge badge-danger">REJECTED</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $status }}</span>
                                                @endif
                                            </td>
                                            <td style="max-width: 300px;">{{ Str::limit($bodyText, 80) }}</td>
                                            <td>
                                                @if($isDefault)
                                                    <span class="badge badge-success">Да</span>
                                                @else
                                                    <form action="{{ route('message-templates.set-default') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="name" value="{{ $name }}">
                                                        <input type="hidden" name="language" value="{{ $lang }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary">По умолчанию</button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-template-btn"
                                                        data-name="{{ $name }}"
                                                        data-language="{{ $lang }}"
                                                        data-category="{{ $category }}"
                                                        data-body="{{ $bodyText }}"
                                                        data-example="">
                                                    Редактировать
                                                </button>
                                                <form action="{{ route('message-templates.destroy', $name) }}" method="POST" class="d-inline" onsubmit="return confirm('Удалить шаблон {{ $name }} (все языки)?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(empty($templates))
                                <p class="text-muted">Нет шаблонов. Создайте шаблон для уведомлений о новых заказах или загрузите из Meta.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addTemplateBtn').addEventListener('click', function() {
        document.getElementById('templateModalTitle').textContent = 'Добавить шаблон';
        document.getElementById('editName').value = '';
        document.getElementById('editLanguage').value = '';
        document.getElementById('templateName').value = '';
        document.getElementById('templateName').readOnly = false;
        document.getElementById('templateLanguage').value = 'ru';
        document.getElementById('templateBody').value = '';
        document.getElementById('templateBodyExample').value = 'Тестовый клиент|TEST-123|50 000';
        document.getElementById('templateIsDefault').checked = false;
    });

    document.querySelectorAll('.edit-template-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('templateModalTitle').textContent = 'Редактировать шаблон (удалить и создать заново)';
            document.getElementById('editName').value = this.dataset.name;
            document.getElementById('editLanguage').value = this.dataset.language;
            document.getElementById('templateName').value = this.dataset.name;
            document.getElementById('templateName').readOnly = true;
            document.getElementById('templateLanguage').value = this.dataset.language;
            document.getElementById('templateBody').value = this.dataset.body || '';
            document.getElementById('templateBodyExample').value = this.dataset.example || 'Тестовый клиент|TEST-123|50 000';
            document.getElementById('templateIsDefault').checked = false;
            $('#templateModal').modal('show');
        });
    });

    var sendTestSelect = document.getElementById('sendTestTemplateName');
    var sendTestLang = document.getElementById('sendTestTemplateLanguage');
    var sendTestBodyParams = document.getElementById('sendTestBodyParams');
    if (sendTestSelect) {
        function updateSendTestFromTemplate() {
            var opt = sendTestSelect.options[sendTestSelect.selectedIndex];
            if (opt && opt.value) {
                if (opt.dataset.language) sendTestLang.value = opt.dataset.language;
                if (opt.dataset.defaultParams !== undefined && sendTestBodyParams) {
                    sendTestBodyParams.value = opt.dataset.defaultParams;
                    sendTestBodyParams.placeholder = opt.dataset.defaultParams || 'value1|value2';
                }
            } else if (sendTestBodyParams) {
                sendTestBodyParams.value = '';
            }
        }
        sendTestSelect.addEventListener('change', updateSendTestFromTemplate);
        updateSendTestFromTemplate();
    }
});
</script>
@endpush
@endsection
