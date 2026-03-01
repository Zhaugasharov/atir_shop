@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            @include('partials.admin-sidebar', ['active' => 'kaspi-status'])
                        </div>
                        <div class="col-md-9">
                            <h4>Статус сервиса Kaspi</h4>
                            <hr class="my-4" style="border-top:1px solid #e0e0e0;">

                            <form action="{{ route('kaspi-status.test') }}" method="POST" class="mb-4">
                                @csrf
                                <button type="submit" class="btn btn-primary">Тест API</button>
                                <small class="text-muted ml-2">Отправит запрос за заказами за последние 24 часа. Результат появится в логах ниже.</small>
                            </form>

                            <div class="card mb-4">
                                <div class="card-header">Последний статус</div>
                                <div class="card-body">
                                    @if($lastLog)
                                        <div class="d-flex align-items-center mb-2">
                                            <strong>Время:</strong>
                                            <span class="ml-2">{{ $lastLog->created_at->format('d.m.Y H:i:s') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <strong>Статус:</strong>
                                            <span class="ml-2">
                                                @if($lastLog->status === \App\Models\KaspiServiceLog::STATUS_SUCCESS)
                                                    <span class="badge badge-success">Успех</span>
                                                @elseif($lastLog->status === \App\Models\KaspiServiceLog::STATUS_ERROR)
                                                    <span class="badge badge-danger">Ошибка</span>
                                                @else
                                                    <span class="badge badge-warning">Предупреждение</span>
                                                @endif
                                            </span>
                                        </div>
                                        @if($lastLog->message)
                                            <div class="mt-2">
                                                <strong>Сообщение:</strong>
                                                <p class="mb-0 mt-1 text-muted">{{ $lastLog->message }}</p>
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">Нет данных. Сервис ещё не выполнялся или миграция не применена.</p>
                                    @endif
                                </div>
                            </div>

                            <h5 class="mt-4 mb-3">Последние ошибки</h5>
                            @if($recentErrors->isNotEmpty())
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Время</th>
                                            <th>Ошибка</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentErrors as $log)
                                            <tr>
                                                <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                                                <td style="max-width: 500px; word-break: break-word;">{{ $log->message }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">Ошибок не было.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
