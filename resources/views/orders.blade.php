@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="list-group list-group-flush">
                                <a href="{{url('home')}}" class="list-group-item list-group-item-action selected">Товары</a>
                                <a href="{{url('orders')}}" class="list-group-item list-group-item-action">Заказы</a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h4>Заказы</h4>
                            <hr class="my-4" style="border-top:1px solid #e0e0e0;">
                            <form method="GET" action="{{ route('orders') }}" class="mb-4">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <input type="text"
                                               name="name"
                                               class="form-control"
                                               placeholder="Номер заказа или номер телефона"
                                               value="{{ request('name') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" class="form-control">
                                            <option value="">Все</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Новый</option>
                                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Выбран</option>
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
                                        <th>Номер клиента</th>
                                        <th>Статус</th>
                                        <th>Тип 1</th>
                                        <th>Тип 2</th>
                                        <th>Тип 3</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td >{{$order->order_id}}</td>
                                            <td>{{$order->phone}}</td>
                                            <td>{{$order->getStatus()}}</td>
                                            <td width="250px">
                                                <p>{{$order->product1->name}}</p>
                                                <img width="100%" src="{{$order->product1->image_url}}" />
                                            </td>
                                            <td width="250px">
                                                <p>{{$order->product2->name}}</p>
                                                <img width="100%" src="{{$order->product2->image_url}}" />
                                            </td>
                                            <td width="250px">
                                                <p>{{$order->product3->name}}</p>
                                                <img width="100%" src="{{$order->product3->image_url}}" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($orders->hasPages())
                                <div class="mt-4">
                                    {{ $orders->withQueryString()->links('pagination::bootstrap-4') }}
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
