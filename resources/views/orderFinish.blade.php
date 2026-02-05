@extends('../layouts/template')

@section('content')
<div class="container my-5">

    <!-- Заголовок -->
    <div class="text-center mb-5">
        <h1 class="fw-bold text-success">{{__('messages.thanks')}}</h1>
        <p class="text-muted mt-2">{{__('messages.goods_bottom')}}</p>
    </div>

    <!-- Блок с товарами -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img src="{{$order->product1->image_url}}" class="card-img-top" alt="Товар 3">
                <div class="card-body">
                    <h5 class="card-title">{{$order->product1->name}}</h5>
                    <p class="card-text text-muted">
                        <span><strong>{{__('messages.article')}}:</strong> {{ $order->product1->article ?? '' }}</span><br/>
                        <span><strong>{{__('messages.gender')}}:</strong> {{ $order->product1->gender ? __('messages.'.$order->product1->gender): '' }}</span>
                    </p>
                </div>
                <div class="card-footer bg-white border-0">
                    @if($order->product1->keywords->count() > 0)
                        @foreach($order->product1->keywords as $keyword)
                            <span class="badge bg-secondary">{{ $keyword->name }}</span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img src="{{$order->product1->image_url}}" class="card-img-top" alt="Товар 3">
                <div class="card-body">
                    <h5 class="card-title">{{$order->product2->name}}</h5>
                    <p class="card-text text-muted">
                        <span><strong>{{__('messages.article')}}:</strong> {{ $order->product2->article ?? '' }}</span><br/>
                        <span><strong>{{__('messages.gender')}}:</strong> {{ $order->product2->gender ? __('messages.'.$order->product2->gender): '' }}</span>
                    </p>
                </div>
                <div class="card-footer bg-white border-0">
                    @if($order->product2->keywords->count() > 0)
                        @foreach($order->product2->keywords as $keyword)
                            <span class="badge bg-secondary">{{ $keyword->name }}</span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img src="{{$order->product1->image_url}}" class="card-img-top" alt="Товар 3">
                <div class="card-body">
                    <h5 class="card-title">{{$order->product3->name}}</h5>
                    <p class="card-text text-muted">
                        <span><strong>{{__('messages.article')}}:</strong> {{ $order->product3->article ?? '' }}</span><br/>
                        <span><strong>{{__('messages.gender')}}:</strong> {{ $order->product3->gender ? __('messages.'.$order->product3->gender): '' }}</span>
                    </p>
                </div>
                <div class="card-footer bg-white border-0">
                    @if($order->product3->keywords->count() > 0)
                        @foreach($order->product3->keywords as $keyword)
                            <span class="badge bg-secondary">{{ $keyword->name }}</span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
