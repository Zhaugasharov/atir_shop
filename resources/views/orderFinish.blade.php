@extends('../layouts/template')

@section('content')
<div class="container finish-container">

    <div class="finish-header">
        <div class="finish-header__icon">
            <i class="bi bi-bag-check-fill"></i>
        </div>
        <h1 class="finish-header__title">{{__('messages.thanks')}}</h1>
        <p class="finish-header__subtitle">{{__('messages.goods_bottom')}}</p>
    </div>

    <div class="row g-4 justify-content-center">
        @foreach([$order->product1, $order->product2, $order->product3] as $product)
        <div class="col-lg-4 col-md-6 col-12">
            <div class="finish-card">
                <div class="finish-card__image">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                    @if($product->is_new)
                        <span class="finish-card__new">{{__('messages.novelties')}}</span>
                    @endif
                    @if($product->article)
                        <span class="finish-card__article">{{ $product->article }}</span>
                    @endif
                    @if($product->gender)
                        <span class="finish-card__quality">{{ __('messages.'.$product->gender) }}</span>
                    @endif
                </div>
                <div class="finish-card__body">
                    <h5 class="finish-card__name">{{ $product->name }}</h5>
                    @if($product->brand)
                        <p class="finish-card__brand">{{ $product->brand->name }}</p>
                    @endif
                    @if($product->keywords->count() > 0)
                        <div class="finish-card__tags">
                            @foreach($product->keywords as $keyword)
                                <span class="finish-card__tag">{{ $keyword->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection

@section('style')
<style>
    :root {
        --fragrancia-green: #2fad66;
        --fragrancia-green-hover: #259a58;
        --fragrancia-border: #e8e8e8;
        --fragrancia-bg: #f5f5f5;
        --fragrancia-text: #333;
        --fragrancia-muted: #888;
    }

    body {
        background: var(--fragrancia-bg);
    }

    .finish-container {
        padding-top: 48px;
        padding-bottom: 60px;
    }

    .finish-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .finish-header__icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 16px;
        background: linear-gradient(135deg, var(--fragrancia-green), #34c97a);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(47, 173, 102, .25);
    }

    .finish-header__icon i {
        font-size: 32px;
        color: #fff;
    }

    .finish-header__title {
        font-size: 28px;
        font-weight: 700;
        color: var(--fragrancia-green);
        margin: 0 0 8px;
    }

    .finish-header__subtitle {
        font-size: 16px;
        color: var(--fragrancia-muted);
        margin: 0;
    }

    .finish-card {
        background: #fff;
        border: 1px solid var(--fragrancia-border);
        border-radius: 14px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .finish-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, .1);
    }

    .finish-card__image {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        max-height: 260px;
        background: #fafafa;
        overflow: hidden;
    }

    .finish-card__image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .finish-card:hover .finish-card__image img {
        transform: scale(1.04);
    }

    .finish-card__new {
        position: absolute;
        top: 12px;
        right: 0;
        background: #e53935;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 12px 4px 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        clip-path: polygon(12px 0, 100% 0, 100% 100%, 0 100%);
        z-index: 2;
    }

    .finish-card__article {
        position: absolute;
        bottom: 36px;
        left: 0;
        background: var(--fragrancia-green);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 0 6px 6px 0;
    }

    .finish-card__quality {
        position: absolute;
        bottom: 10px;
        left: 0;
        background: rgba(47, 173, 102, .85);
        color: #fff;
        font-size: 11px;
        font-weight: 500;
        padding: 2px 10px;
        border-radius: 0 6px 6px 0;
        backdrop-filter: blur(4px);
    }

    .finish-card__body {
        flex: 1;
        padding: 16px 18px 20px;
        display: flex;
        flex-direction: column;
    }

    .finish-card__name {
        font-size: 17px;
        font-weight: 600;
        color: var(--fragrancia-text);
        margin: 0 0 4px;
        line-height: 1.35;
    }

    .finish-card__brand {
        font-size: 13px;
        color: var(--fragrancia-muted);
        font-style: italic;
        margin: 0 0 12px;
    }

    .finish-card__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: auto;
    }

    .finish-card__tag {
        display: inline-block;
        padding: 4px 12px;
        font-size: 12px;
        border: 1px solid var(--fragrancia-border);
        border-radius: 20px;
        color: var(--fragrancia-text);
        background: #fff;
        transition: all 0.2s;
    }

    .finish-card__tag:hover {
        border-color: var(--fragrancia-green);
        color: var(--fragrancia-green);
    }

    @media (max-width: 768px) {
        .finish-container {
            padding-top: 32px;
            padding-bottom: 40px;
        }

        .finish-header__icon {
            width: 56px;
            height: 56px;
        }

        .finish-header__icon i {
            font-size: 24px;
        }

        .finish-header__title {
            font-size: 22px;
        }

        .finish-header__subtitle {
            font-size: 14px;
        }

        .finish-card__image {
            aspect-ratio: 1 / 1;
            max-height: 200px;
        }
    }
</style>
@endsection
