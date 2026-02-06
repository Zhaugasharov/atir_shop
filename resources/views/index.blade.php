@extends('../layouts/template')

@section('content')
<div class="container my-5">
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="row">
                <div class="col-xs-5 col-md-9">
                    <input type="text"
                           id="query"
                           name="query"
                           class="form-control mb-3"
                           placeholder="{{__('messages.search_params')}}"
                           value="{{ request('query') }}">
                </div>
                <div class="col-xs-7 col-md-3">
                    <select id="gender" class="mb-3 form-control">
                        <option>{{__('messages.all')}}</option>
                        <option value="male">{{__('messages.male')}}</option>
                        <option value="female">{{__('messages.female')}}</option>
                        <option value="unisex">{{__('messages.unisex')}}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="product-table"></div>

    <div class="spinner-border text-primary my-3 text-center"  id="loader" style="position: absolute; left: 0; right: 0; margin: 0 auto;">
        <span class="visually-hidden">{{__('messages.loading')}}...</span>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let page = 1;
let loading = false;
let lastPage = false;

function loadProducts() {
    if (loading || lastPage) return;

    loading = true;
    $('#loader').show();

    var query = $("#query").val();
    var gender = $("#gender").val();

    $.ajax({
        url: "{{ url('/api/products') }}",
        data: { page: page, query: query, gender: gender, locale: document.documentElement.lang, homePage: 1},
        success: function (data) {
            if (data.trim() === '') {
                lastPage = true;
            } else {
                $('#product-table').append(data);
                page++;
            }
        },
        complete: function () {
            loading = false;
            $('#loader').hide();
        },
        error: function () {
            loading = false;
            $('#loader').hide();
            alert('Ошибка загрузки');
        }
    });
}

$("#app").on("change", "#gender", function () {
    page = 1;
    loading = false;
    lastPage = false;
    $("#product-table").html("");
    loadProducts();
});

$("#app").on("keyup", "#query", function () {
    page = 1;
    loading = false;
    lastPage = false;
    $("#product-table").html("");
    loadProducts();
});

// первая загрузка
loadProducts();

// отслеживаем скролл
$(window).on('scroll', function () {
    if ($(window).scrollTop() + $(window).height() >= $(document).height() - 150) {
        loadProducts();
    }
});

</script>
@endsection
