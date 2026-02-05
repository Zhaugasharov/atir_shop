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

    <div class="text-center my-3" id="loader" style="display:none;">
        <span>Загрузка...</span>
    </div>
</div>
<footer class="footer">
    <div class="container">
        <div class="row">
            <h5>Выберите 3 парьфюм для бокса</h5>
        </div>
        <div class="row">
            <button class="btn btn-success">Выбрать</button>
        </div>
    </div>
</footer>
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
        url: "{{ route('apiProducts') }}",
        data: { page: page, query: query, gender: gender },
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

@section('style')
<style>
 .footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    height: 150px;
    background-color: #ffffff;
    border-top: 1px solid #f0f0f0;
    margin: 0;
    padding: 0;
    z-index: 1;
 }

.good-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    padding: 0.5rem 0px 4rem;
    max-width: 100%;
}

.filter {
    position: relative;
    top: -10px;
    padding: 0;
    text-align: center;
    padding-bottom: 8px;
    cursor: pointer;
}
/* Extra small devices (phones, 600px and down) */
@media only screen and (max-width: 600px) {
    .good-grid {
        grid-template-columns: repeat(1, 1fr);
    }
}

/* Small devices (portrait tablets and large phones, 600px and up) */
@media only screen and (min-width: 600px) and (max-width: 830px) {
    .good-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Small devices (portrait tablets and large phones, 600px and up) */
@media only screen and (min-width: 830px) and (max-width: 973px) {
    .good-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

</style>
@endsection
