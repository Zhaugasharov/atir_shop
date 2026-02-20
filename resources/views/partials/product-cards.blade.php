@foreach($products as $product)
    <div id="product_{{$product->id}}" product-id="{{$product->id}}" class="col-md-6 mb-3 card-item">
        <div class="product-card">
            <div class="product-card__image">
                <img id="product_img_{{$product->id}}" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                @if($product->is_new)
                    <span class="product-card__new-flag">{{__('messages.novelties')}}</span>
                @endif
                <span class="product-card__badge">{{ $product->article ?? '' }}</span>
                @if($product->quality)
                    <span class="product-card__type">{{ $product->quality == 'premium' ? __('messages.premium') : __('messages.top') }}</span>
                @elseif($product->gender)
                    <span class="product-card__type">{{ __('messages.'.$product->gender) }}</span>
                @endif
            </div>
            <div class="product-card__info">
                <h5 id="product_title_{{$product->id}}" class="product-card__name">{{ $product->name }}</h5>
                @if($product->brand)
                    <p class="product-card__brand">{{ $product->brand->name }}</p>
                @endif
                @if($product->keywords->count() > 0)
                    <p class="product-card__keywords">
                        {{ $product->keywords->pluck('name')->implode(', ') }}
                    </p>
                @endif
                <div class="product-card__tags">
                    @foreach($product->keywords as $keyword)
                        <span class="product-card__tag">{{ $keyword->name }}</span>
                    @endforeach
                </div>
                <div class="product-card__actions">
                    @if(empty($homePage))
                        <button type="button" product-id="{{$product->id}}" class="product-add float-end btn-fragrancia-select">
                            <i class="bi bi-plus-circle"></i> <span>{{__('messages.select')}}</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
