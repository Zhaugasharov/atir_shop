@foreach($products as $product)
    <div id="product_{{$product->id}}" product-id="{{$product->id}}}" class="col-md-4 mb-4 card-item">
        <div class="card h-100 shadow-sm">
            <img id="product_img_{{$product->id}}" src="{{ $product->image_url }}"
                 class="card-img-top"
                 style="height:220px; object-fit:cover"
                 alt="{{ $product->name }}">

            <div class="card-body">
                <h5 id="product_title_{{$product->id}}" class="card-title">{{ $product->name }}</h5>
                <p class="card-text small">
                    <span><strong>{{__('messages.article')}}:</strong> {{ $product->article ?? '' }}</span><br/>
                    <span><strong>{{__('messages.gender')}}:</strong> {{ $product->gender ? __('messages.'.$product->gender): '' }}</span>
                </p>
                <p class="card-text">
                    @if($product->keywords->count() > 0)
                        @foreach($product->keywords as $keyword)
                            <span class="badge bg-secondary">{{ $keyword->name }}</span>
                        @endforeach
                    @endif
                </p>
            </div>
            <div class="card-body">
                @if(empty($homePage))
                    <button type="button" product-id="{{$product->id}}" class="product-add float-end btn btn-outline-success"><i class="fa fa-plus"></i>{{__('messages.select')}}</button>
                @else
                    <a style="width: 150px" class="btn btn-danger" href="https://kaspi.kz/shop/p/c-{{ $product->article ?? '' }}" target="_blank">Kaspi</a>
                @endif
            </div>
        </div>
    </div>
@endforeach
