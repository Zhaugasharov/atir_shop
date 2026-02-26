@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            @include('partials.admin-sidebar', ['active' => 'broadcasts'])
                        </div>
                        <div class="col-md-9">
                            <h4>Рассылки</h4>
                            <hr class="my-4" style="border-top:1px solid #e0e0e0;">
                            <form method="GET" action="{{ route('broadcasts') }}" class="mb-4">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <input type="text"
                                               name="search"
                                               class="form-control"
                                               placeholder="Номер заказа, телефон или текст сообщения"
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="delivery_status" class="form-control">
                                            <option value="">Все статусы</option>
                                            <option value="sent" {{ request('delivery_status') == 'sent' ? 'selected' : '' }}>Отправлено</option>
                                            <option value="delivered" {{ request('delivery_status') == 'delivered' ? 'selected' : '' }}>Доставлено</option>
                                            <option value="failed" {{ request('delivery_status') == 'failed' ? 'selected' : '' }}>Не доставлено</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-dark btn-block">Найти</button>
                                    </div>
                                </div>
                            </form>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Номер заказа</th>
                                        <th>Телефон</th>
                                        <th>Сообщение</th>
                                        <th>Статус</th>
                                        <th>Дата</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($broadcast_messages as $bm)
                                        <tr>
                                            <td>{{ $bm->order ? $bm->order->order_id : '-' }}</td>
                                            <td>{{ $bm->phone }}</td>
                                            <td style="max-width: 300px;">{{ Str::limit($bm->message, 80) }}</td>
                                            <td>{{ $bm->getStatusLabel() }}</td>
                                            <td>{{ $bm->created_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($broadcast_messages->isEmpty())
                                <p class="text-muted">Нет рассылок</p>
                            @endif
                            @if($broadcast_messages->hasPages())
                                <div class="mt-4">
                                    {{ $broadcast_messages->withQueryString()->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
