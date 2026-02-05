@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
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
                            <form method="GET" action="{{ route('home') }}" class="mb-4">
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <input type="text"
                                               name="name"
                                               class="form-control"
                                               placeholder="Номер заказа"
                                               value="{{ request('name') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <input type="text"
                                               name="phone"
                                               class="form-control"
                                               placeholder="Номер клиента"
                                               value="{{ request('name') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <select name="status" class="form-control">
                                            <option value="">Все</option>
                                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Мужской</option>
                                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Женский</option>
                                            <option value="unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>Унисекс</option>
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

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
