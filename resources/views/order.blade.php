@extends('../layouts/template')

@section('content')
<div id="sideMenu" class="side-menu">
    <div class="side-menu-header">
        <span>{{__('messages.selected')}}</span>
        <button id="closeMenu" class="btn btn-sm btn-light">✕</button>
    </div>
    <div class="selected-block pb-5">
        <ul class="side-menu-list mt-3">
            <li>
                <div id="selected_pr_1" style="display:none" class="card shadow-sm hidden">
                    <img id="selected_pr_img_1"  class="card-img-top" src="" />
                    <div class="card-body">
                        <h5 id="selected_pr_text_1" class="card-title"></h5>
                    </div>
                    <div class="card-body">
                        <button type="button" data="1" class="product-remove float-end btn btn-outline-danger"><i class="fa fa-times"></i>{{__('messages.remove')}}</button>
                    </div>
                </div>
            </li>
            <li>
                <div id="selected_pr_2" style="display:none" class="card shadow-sm hidden">
                    <img id="selected_pr_img_2"  class="card-img-top" src="" />
                    <div class="card-body">
                        <h5 id="selected_pr_text_2" class="card-title"></h5>
                    </div>
                    <div class="card-body">
                        <button type="button" data="2" class="product-remove float-end btn btn-outline-danger"><i class="fa fa-times"></i>{{__('messages.remove')}}</button>
                    </div>
                </div>
            </li>
            <li>
                <div id="selected_pr_3"  style="display:none" class="card shadow-sm">
                    <img id="selected_pr_img_3"  class="card-img-top" src="" />
                    <div class="card-body">
                        <h5 id="selected_pr_text_3" class="card-title"></h5>
                    </div>
                    <div class="card-body">
                        <button type="button" data="3" class="product-remove float-end btn btn-outline-danger"><i class="fa fa-times"></i>{{__('messages.remove')}}</button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>

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
<footer class="footer">
    <div class="container pt-2">
        <div class="row">
            <h5>{{__('messages.selectTT')}}</h5>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
               <button type="button" id="openMenu" class="btn btn-primary position-relative">
                 {{__('messages.selected')}}
                 <span id="product-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
               </button>
                <form id="saveOrderForm" style="display: none" class="float-end" action="{{route('saveOrder', ['orderId' => $orderId])}}" method="POST">
                    @csrf
                    <input id="product_input_1" name="product_1" type="hidden" value=""/>
                    <input id="product_input_2" name="product_2" type="hidden" value=""/>
                    <input id="product_input_3" name="product_3" type="hidden" value=""/>
                    <button style="min-width:120px;" type="submit" class="btn btn-pulse btn-outline-success">{{__('messages.order')}}</button>
                </form>
            </div>
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
let count = 0;
let selectedProducts = {
    product1: 0,
    product2: 0,
    product3: 0,
};

$("#product-table").on('click', '.product-add', function(){
    let productId = $(this).attr("product-id");
    if(!selectedProducts.product1) {
        $("#selected_pr_img_1").attr('src', $("#product_img_" + productId).attr('src'));
        $("#selected_pr_text_1").html($("#product_title_" + productId).html());
        selectedProducts.product1 = productId;
        $("#product_input_1").val(productId);
        $("#selected_pr_1").show();
        counter();
        checkReady();
        return;
    }

    if(!selectedProducts.product2) {
        $("#selected_pr_img_2").attr('src', $("#product_img_" + productId).attr('src'));
        $("#selected_pr_text_2").html($("#product_title_" + productId).html());
        selectedProducts.product2 = productId;
        $("#product_input_2").val(productId);
        $("#selected_pr_2").show();
        counter();
        checkReady();
        return;
    }

    if(!selectedProducts.product3) {
        $("#selected_pr_img_3").attr('src', $("#product_img_" + productId).attr('src'));
        $("#selected_pr_text_3").html($("#product_title_" + productId).html());
        selectedProducts.product3 = productId;
        $("#product_input_3").val(productId);
        $("#selected_pr_3").show();
        counter();
        checkReady();
        return;
    }

});

$(".product-remove").click(function(){
    let product = $(this).attr('data');
    selectedProducts['product' + product] = 0;
    $("#selected_pr_" + product).hide();
    $("#product_input_1").val('');
    $("#product_input_2").val('');
    $("#product_input_3").val('');
    checkReady();
    minus();
});

function minus() {
    count -= 1;
    $("#product-count").html(count);
}

function counter() {
    count += 1;
    $("#product-count").html(count);
}

function checkReady() {
    if(selectedProducts.product1 && selectedProducts.product2 && selectedProducts.product3) {
        $("#saveOrderForm").show();
        return;
    }
    $("#saveOrderForm").hide();
}

$('#openMenu').on('click', function () {
    $('#sideMenu').addClass('active');
    $('#menuOverlay').addClass('active');
});

$('#closeMenu, #menuOverlay').on('click', function () {
    $('#sideMenu').removeClass('active');
    $('#menuOverlay').removeClass('active');
});

$.ajaxSetup({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept-Language': document.documentElement.lang
    }
});

function loadProducts() {
    if (loading || lastPage) return;

    loading = true;
    $('#loader').show();

    var query = $("#query").val();
    var gender = $("#gender").val();

    $.ajax({
        url: "{{ url('/api/products') }}",
        data: { page: page, query: query, gender: gender, locale: document.documentElement.lang },
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
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(25,135,84,.6);
        }
        70% {
            box-shadow: 0 0 0 12px rgba(25,135,84,0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(25,135,84,0);
        }
    }

    .btn-pulse {
        animation: pulse 1s infinite;
    }

    .selected-block {
        overflow-y: auto;
        height: 100%;
    }
    .side-menu {
        position: fixed;
        top: 0;
        left: -280px;
        width: 280px;
        height: 100%;
        background: #fff;
        box-shadow: 2px 0 15px rgba(0,0,0,.2);
        transition: left 0.3s ease;
        z-index: 1050;
        padding: 15px;
    }

    .side-menu.active {
        left: 0;
    }

    .side-menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .side-menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .side-menu-list li {
        margin-bottom: 10px;
    }

    .side-menu-list a {
        text-decoration: none;
        color: #333;
        display: block;
        padding: 8px 10px;
        border-radius: 6px;
    }

    .side-menu-list a:hover {
        background: #f1f1f1;
    }

    /* overlay */
    #menuOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,.4);
        opacity: 0;
        visibility: hidden;
        transition: 0.3s;
        z-index: 1040;
    }

    #menuOverlay.active {
        opacity: 1;
        visibility: visible;
    }

    .product-selected {
        width: 100%;
    }

    .product-selected img {
        width: 100%;
    }

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
