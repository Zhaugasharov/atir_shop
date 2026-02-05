@foreach($products as $product)
    <div class="col-md-4 mb-4 card-item">
        <div class="card h-100 shadow-sm">
            <img src="{{ $product->image_url }}"
                 class="card-img-top"
                 style="height:220px; object-fit:cover"
                 alt="{{ $product->name }}">

            <div class="card-body">
                <h5 class="card-title">{{ $product->name }}</h5>

                <p class="small">
                    <strong>Артикул:</strong> {{ $product->article ?? 'не указан' }}
                </p>
            </div>
        </div>
    </div>
@endforeach
