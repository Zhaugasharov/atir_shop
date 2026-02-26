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
                            <hr class="my-4" style="border-top:1px solid #e0e0e0;">

                            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#templateModal" id="addTemplateBtn">
                                Добавить шаблон
                            </button>
                            <button class="btn btn-outline-success mb-3" data-toggle="modal" data-target="#sendTestModal" id="sendTestBtn" @if($templates->isEmpty()) disabled @endif>
                                Отправить тестовое сообщение
                            </button>

                            <div class="modal fade" id="templateModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <form id="templateForm" action="{{ route('message-templates.store') }}" method="POST" class="modal-content">
                                        @csrf
                                        <input type="hidden" name="template_id" id="templateId">

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="templateModalTitle">Добавить шаблон</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Название</label>
                                                <input type="text" name="name" class="form-control" required id="templateName" value="{{ old('name') }}" placeholder="Например: Уведомление о заказе">
                                            </div>
                                            <div class="form-group">
                                                <label>Текст сообщения</label>
                                                <textarea name="body" class="form-control" rows="6" required id="templateBody" placeholder="Здравствуйте! Ваш заказ {order_id} на сумму {total_price} тг принят.">{{ old('body') }}</textarea>
                                                <small class="form-text text-muted">
                                                    Доступные плейсхолдеры:
                                                    @foreach(\App\Models\MessageTemplate::getPlaceholders() as $placeholder => $label)
                                                        <code>{{ $placeholder }}</code> ({{ $label }})
                                                    @endforeach
                                                </small>
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
                                                <select name="template_id" class="form-control" required>
                                                    <option value="">Выберите шаблон</option>
                                                    @foreach($templates as $t)
                                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Номер телефона</label>
                                                <input type="text" name="phone" class="form-control" required
                                                       placeholder="77001234567 или 8 700 123 45 67"
                                                       value="{{ old('phone') }}">
                                                <small class="form-text text-muted">В международном формате (например: 77001234567)</small>
                                            </div>
                                            <p class="text-muted small mb-0">Плейсхолдеры будут заменены на тестовые данные: order_id=TEST-123, order_link=ссылка на заказ, total_price=50 000, customer_name=Тестовый клиент</p>
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
                                        <th>Текст</th>
                                        <th>По умолчанию</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templates as $t)
                                        <tr>
                                            <td>{{ $t->name }}</td>
                                            <td style="max-width: 400px;">{{ Str::limit($t->body, 100) }}</td>
                                            <td>
                                                @if($t->is_default)
                                                    <span class="badge badge-success">Да</span>
                                                @else
                                                    <form action="{{ route('message-templates.set-default', $t->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Сделать по умолчанию</button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-template-btn"
                                                        data-id="{{ $t->id }}"
                                                        data-name="{{ $t->name }}"
                                                        data-body="{{ $t->body }}"
                                                        data-default="{{ $t->is_default ? '1' : '0' }}">
                                                    Редактировать
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($templates->isEmpty())
                                <p class="text-muted">Нет шаблонов. Создайте шаблон для уведомлений о новых заказах.</p>
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
        document.getElementById('templateId').value = '';
        document.getElementById('templateName').value = '';
        document.getElementById('templateBody').value = '';
        document.getElementById('templateIsDefault').checked = false;
    });

    document.querySelectorAll('.edit-template-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('templateModalTitle').textContent = 'Редактировать шаблон';
            document.getElementById('templateId').value = this.dataset.id;
            document.getElementById('templateName').value = this.dataset.name;
            document.getElementById('templateBody').value = this.dataset.body;
            document.getElementById('templateIsDefault').checked = this.dataset.default === '1';
            $('#templateModal').modal('show');
        });
    });
});
</script>
@endpush
@endsection
