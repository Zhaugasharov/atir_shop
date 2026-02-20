@extends('../layouts/template')

@section('content')
<div id="menuOverlay"></div>

<div id="sideMenu" class="side-menu">
    <div class="side-menu-header">
        <span>{{__('messages.selected')}}</span>
        <button id="closeMenu" class="btn btn-sm btn-light">✕</button>
    </div>
    <div class="selected-block pb-5">
        <ul class="side-menu-list mt-3">
            <li>
                <div id="selected_pr_1" style="display:none" class="cart-card">
                    <span class="cart-card__num">1</span>
                    <div class="cart-card__image">
                        <img id="selected_pr_img_1" src="" alt="" />
                    </div>
                    <div class="cart-card__info">
                        <h6 id="selected_pr_text_1" class="cart-card__name"></h6>
                        <button type="button" data="1" class="product-remove cart-card__remove"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            </li>
            <li>
                <div id="selected_pr_2" style="display:none" class="cart-card">
                    <span class="cart-card__num">2</span>
                    <div class="cart-card__image">
                        <img id="selected_pr_img_2" src="" alt="" />
                    </div>
                    <div class="cart-card__info">
                        <h6 id="selected_pr_text_2" class="cart-card__name"></h6>
                        <button type="button" data="2" class="product-remove cart-card__remove"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            </li>
            <li>
                <div id="selected_pr_3" style="display:none" class="cart-card">
                    <span class="cart-card__num">3</span>
                    <div class="cart-card__image">
                        <img id="selected_pr_img_3" src="" alt="" />
                    </div>
                    <div class="cart-card__info">
                        <h6 id="selected_pr_text_3" class="cart-card__name"></h6>
                        <button type="button" data="3" class="product-remove cart-card__remove"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>

<div id="filterOverlay" class="filter-overlay"></div>
<div id="filterPanel" class="filter-panel">
    <div class="filter-panel__header">
        <span class="filter-panel__title">{{__('messages.filters_title')}}</span>
        <button id="closeFilter" class="filter-panel__close">&times;</button>
    </div>
    <div class="filter-panel__body">
        <div class="filter-group">
            <label class="filter-group__label">{{__('messages.brand')}}</label>
            <select id="filterBrand" class="filter-select filter-select--full">
                <option value="">{{__('messages.all')}}</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-group__label">{{__('messages.gender_label')}}</label>
            <div class="filter-chips" id="filterGenderChips">
                <button type="button" class="filter-chip" data-value="male">{{__('messages.male')}}</button>
                <button type="button" class="filter-chip" data-value="female">{{__('messages.female')}}</button>
                <button type="button" class="filter-chip" data-value="unisex">{{__('messages.unisex')}}</button>
            </div>
        </div>
        <div class="filter-group">
            <label class="filter-group__label">{{__('messages.quality_label')}}</label>
            <div class="filter-chips" id="filterQualityChips">
                <button type="button" class="filter-chip" data-value="premium">{{__('messages.premium')}}</button>
                <button type="button" class="filter-chip" data-value="top">{{__('messages.top')}}</button>
            </div>
        </div>
        <div class="filter-group">
            <label class="filter-group__label">{{__('messages.novelties')}}</label>
            <label class="filter-toggle">
                <input type="checkbox" id="filterIsNew">
                <span class="filter-toggle__slider"></span>
                <span class="filter-toggle__text">{{__('messages.novelties')}}</span>
            </label>
        </div>
    </div>
    <div class="filter-panel__footer">
        <button type="button" id="applyFilters" class="filter-btn filter-btn--apply">{{__('messages.apply')}}</button>
        <button type="button" id="resetFilters" class="filter-btn filter-btn--reset">{{__('messages.reset_filters')}}</button>
    </div>
</div>

<div class="search-bar">
    <div class="container">
        <div class="search-bar__inner">
            <div class="search-wrapper">
                <i class="bi bi-search search-wrapper__icon"></i>
                <input type="text"
                       id="query"
                       name="query"
                       class="search-wrapper__input"
                       placeholder="{{__('messages.search_params')}}"
                       autocomplete="off"
                       value="{{ request('query') }}">
                <div id="searchDropdown" class="search-dropdown"></div>
            </div>
            <div class="search-bar__filters">
                <button type="button" id="openFilter" class="cart-btn" title="Фильтры">
                    <i class="bi bi-funnel"></i>
                </button>
                <button type="button" id="openMenu" class="cart-btn position-relative">
                    <i class="bi bi-cart3"></i>
                    <span id="product-count" class="cart-btn__badge">0</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container catalog-container">
    <div class="row" id="product-table"></div>

    <div class="spinner-border text-success my-3" id="loader" style="display:block; margin: 0 auto;">
        <span class="visually-hidden">{{__('messages.loading')}}...</span>
    </div>
</div>

<footer class="footer">
    <div class="container pt-2">
        <div class="footer__inner">
            <h5 class="footer__title">{{__('messages.selectTT')}}</h5>
            <div class="footer__actions">
                <form id="saveOrderForm" style="display: none" action="{{route('saveOrder', ['orderId' => $orderId])}}" method="POST">
                    @csrf
                    <input id="product_input_1" name="product_1" type="hidden" value=""/>
                    <input id="product_input_2" name="product_2" type="hidden" value=""/>
                    <input id="product_input_3" name="product_3" type="hidden" value=""/>
                    <button type="submit" class="btn-fragrancia btn-fragrancia--lg btn-pulse"><i class="bi bi-bag-check-fill"></i> {{__('messages.order')}}</button>
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
let suggestTimer = null;
let productTimer = null;
let selectedProducts = {
    product1: 0,
    product2: 0,
    product3: 0,
};

let activeFilters = {
    gender: '',
    brand_id: '',
    quality: '',
    is_new: ''
};

// Load brands for filter dropdown
$.ajax({
    url: "{{ url('/api/brands') }}",
    dataType: 'json',
    success: function(brands) {
        var $select = $('#filterBrand');
        brands.forEach(function(b) {
            $select.append('<option value="' + b.id + '">' + b.name + '</option>');
        });
    }
});

// Filter panel open/close
$('#openFilter').on('click', function() {
    $('#filterPanel').addClass('active');
    $('#filterOverlay').addClass('active');
});

$('#closeFilter, #filterOverlay').on('click', function() {
    $('#filterPanel').removeClass('active');
    $('#filterOverlay').removeClass('active');
});

// Chip toggle
$('.filter-chips').on('click', '.filter-chip', function() {
    $(this).toggleClass('active');
});

// Apply filters
$('#applyFilters').on('click', function() {
    var genderValues = [];
    $('#filterGenderChips .filter-chip.active').each(function() {
        genderValues.push($(this).data('value'));
    });
    activeFilters.gender = genderValues.join(',');
    activeFilters.brand_id = $('#filterBrand').val();

    var qualityValues = [];
    $('#filterQualityChips .filter-chip.active').each(function() {
        qualityValues.push($(this).data('value'));
    });
    activeFilters.quality = qualityValues.join(',');

    activeFilters.is_new = $('#filterIsNew').is(':checked') ? '1' : '';

    $('#filterPanel').removeClass('active');
    $('#filterOverlay').removeClass('active');

    page = 1;
    loading = false;
    lastPage = false;
    $("#product-table").html("");
    loadProducts();
});

// Reset filters
$('#resetFilters').on('click', function() {
    activeFilters = { gender: '', brand_id: '', quality: '', is_new: '' };
    $('#filterBrand').val('');
    $('.filter-chip').removeClass('active');
    $('#filterIsNew').prop('checked', false);

    $('#filterPanel').removeClass('active');
    $('#filterOverlay').removeClass('active');

    page = 1;
    loading = false;
    lastPage = false;
    $("#product-table").html("");
    loadProducts();
});

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
    $("#product_input_" + product).val('');
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

    $.ajax({
        url: "{{ url('/api/products') }}",
        data: {
            page: page,
            query: query,
            gender: activeFilters.gender,
            brand_id: activeFilters.brand_id,
            quality: activeFilters.quality,
            is_new: activeFilters.is_new,
            locale: document.documentElement.lang
        },
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
        }
    });
}

function searchSuggest(query) {
    if (query.length < 1) {
        $('#searchDropdown').hide().empty();
        return;
    }

    $.ajax({
        url: "{{ url('/api/search-suggest') }}",
        data: { query: query },
        dataType: 'json',
        success: function (items) {
            var $dropdown = $('#searchDropdown');
            $dropdown.empty();

            if (items.length === 0) {
                $dropdown.hide();
                return;
            }

            items.forEach(function(item) {
                var genderLabel = item.gender || '';
                var html = '<div class="search-dropdown__item" data-name="' + item.name + '">' +
                    '<div class="search-dropdown__left">' +
                        '<img src="' + item.image_url + '" alt="">' +
                    '</div>' +
                    '<div class="search-dropdown__right">' +
                        '<span class="search-dropdown__article">' + (item.article || '') + ' (' + genderLabel + ')</span>' +
                        '<span class="search-dropdown__keywords">' + (item.keywords_string || '') + '</span>' +
                        '<strong class="search-dropdown__name">' + item.name + '</strong>' +
                    '</div>' +
                '</div>';
                $dropdown.append(html);
            });

            $dropdown.show();
        }
    });
}

$('#searchDropdown').on('click', '.search-dropdown__item', function() {
    var name = $(this).data('name');
    $('#query').val(name);
    $('#searchDropdown').hide().empty();
    page = 1;
    loading = false;
    lastPage = false;
    $("#product-table").html("");
    loadProducts();
});

$(document).on('click', function(e) {
    if (!$(e.target).closest('.search-wrapper').length) {
        $('#searchDropdown').hide();
    }
});

$("#app").on("keyup", "#query", function () {
    var val = $(this).val();

    clearTimeout(suggestTimer);
    suggestTimer = setTimeout(function() {
        searchSuggest(val);
    }, 200);

    clearTimeout(productTimer);
    productTimer = setTimeout(function() {
        page = 1;
        loading = false;
        lastPage = false;
        $("#product-table").html("");
        loadProducts();
    }, 400);
});

loadProducts();

$(window).on('scroll', function () {
    if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200) {
        loadProducts();
    }
});
</script>
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

    /* ===== Search Bar ===== */
    .search-bar {
        background: #fff;
        border-bottom: 1px solid var(--fragrancia-border);
        padding: 12px 0;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .search-bar__inner {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .search-wrapper {
        position: relative;
        flex: 1;
    }

    .search-wrapper__icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--fragrancia-muted);
        font-size: 16px;
    }

    .search-wrapper__input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border: 2px solid var(--fragrancia-green);
        border-radius: 8px;
        font-size: 15px;
        outline: none;
        transition: border-color 0.2s;
    }

    .search-wrapper__input:focus {
        border-color: var(--fragrancia-green-hover);
        box-shadow: 0 0 0 3px rgba(47, 173, 102, 0.15);
    }

    .search-wrapper__input::placeholder {
        color: #aaa;
    }

    .search-bar__filters {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .filter-select {
        padding: 10px 14px;
        border: 1px solid var(--fragrancia-border);
        border-radius: 8px;
        font-size: 14px;
        background: #fff;
        cursor: pointer;
        outline: none;
        min-width: 120px;
    }

    .filter-select:focus {
        border-color: var(--fragrancia-green);
    }

    .cart-btn {
        width: 44px;
        height: 44px;
        border: 1px solid var(--fragrancia-border);
        border-radius: 8px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 20px;
        color: var(--fragrancia-text);
        transition: border-color 0.2s;
    }

    .cart-btn:hover {
        border-color: var(--fragrancia-green);
        color: var(--fragrancia-green);
    }

    .cart-btn__badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: var(--fragrancia-green);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ===== Search Dropdown ===== */
    .search-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid var(--fragrancia-border);
        border-top: none;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
        z-index: 200;
        max-height: 400px;
        overflow-y: auto;
    }

    .search-dropdown__item {
        display: flex;
        align-items: center;
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.15s;
    }

    .search-dropdown__item:last-child {
        border-bottom: none;
    }

    .search-dropdown__item:hover {
        background: #f8faf9;
    }

    .search-dropdown__left {
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        margin-right: 12px;
    }

    .search-dropdown__left img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 6px;
    }

    .search-dropdown__right {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .search-dropdown__article {
        font-size: 12px;
        color: var(--fragrancia-muted);
    }

    .search-dropdown__keywords {
        font-size: 12px;
        color: #aaa;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .search-dropdown__name {
        font-size: 14px;
        color: var(--fragrancia-text);
    }

    /* ===== Catalog ===== */
    .catalog-container {
        padding-top: 20px;
        padding-bottom: 180px;
    }

    /* ===== Product Card (Fragrancia style) ===== */
    .product-card {
        display: flex;
        background: #fff;
        border: 1px solid var(--fragrancia-border);
        border-radius: 10px;
        overflow: hidden;
        transition: box-shadow 0.2s;
    }

    .product-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,.08);
    }

    .product-card__image {
        width: 140px;
        min-height: 160px;
        flex-shrink: 0;
        position: relative;
        background: #fafafa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-card__image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-card__badge {
        position: absolute;
        bottom: 28px;
        left: 0;
        background: var(--fragrancia-green);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 0 4px 4px 0;
    }

    .product-card__new-flag {
        position: absolute;
        top: 0;
        right: 0;
        background: #e53935;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px 4px 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        clip-path: polygon(12px 0, 100% 0, 100% 100%, 0 100%);
        z-index: 2;
    }

    .product-card__type {
        position: absolute;
        bottom: 6px;
        left: 0;
        background: var(--fragrancia-green);
        color: #fff;
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 0 4px 4px 0;
    }

    .product-card__info {
        flex: 1;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
    }

    .product-card__name {
        font-size: 16px;
        font-weight: 600;
        color: var(--fragrancia-text);
        margin: 0 0 4px;
        line-height: 1.3;
    }

    .product-card__brand {
        font-size: 13px;
        color: var(--fragrancia-muted);
        margin: 0 0 4px;
        font-style: italic;
    }

    .product-card__keywords {
        font-size: 13px;
        color: var(--fragrancia-muted);
        margin: 0 0 8px;
    }

    .product-card__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 12px;
    }

    .product-card__tag {
        display: inline-block;
        padding: 3px 10px;
        font-size: 12px;
        border: 1px solid var(--fragrancia-border);
        border-radius: 20px;
        color: var(--fragrancia-text);
        background: #fff;
    }

    .product-card__actions {
        margin-top: auto;
    }

    .btn-fragrancia-select {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 22px;
        font-size: 13px;
        font-weight: 600;
        color: var(--fragrancia-green);
        background: #fff;
        border: 2px solid var(--fragrancia-green);
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.25s ease;
    }

    .btn-fragrancia-select i {
        font-size: 16px;
        transition: transform 0.25s ease;
    }

    .btn-fragrancia-select:hover {
        color: #fff;
        background: var(--fragrancia-green);
        border-color: var(--fragrancia-green);
        box-shadow: 0 4px 12px rgba(47, 173, 102, 0.35);
        transform: translateY(-1px);
    }

    .btn-fragrancia-select:hover i {
        transform: rotate(90deg);
    }

    .btn-fragrancia-select:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(47, 173, 102, 0.25);
    }

    .btn-fragrancia {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 20px;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        background: var(--fragrancia-green);
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .btn-fragrancia:hover {
        background: var(--fragrancia-green-hover);
        color: #fff;
    }

    .btn-fragrancia--lg {
        padding: 12px 32px;
        font-size: 15px;
    }

    .btn-kaspi {
        background: #f14635;
    }

    .btn-kaspi:hover {
        background: #d93c2d;
    }

    /* ===== Footer ===== */
    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        background-color: #fff;
        border-top: 1px solid var(--fragrancia-border);
        padding: 0;
        z-index: 50;
    }

    .footer__inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 0;
    }

    .footer__title {
        font-size: 16px;
        font-weight: 600;
        color: var(--fragrancia-text);
        margin: 0;
    }

    /* ===== Side Menu ===== */
    .side-menu {
        position: fixed;
        top: 0;
        left: -320px;
        width: 320px;
        height: 100%;
        background: #fff;
        box-shadow: 2px 0 15px rgba(0,0,0,.2);
        transition: left 0.3s ease;
        z-index: 1050;
        padding: 20px;
    }

    .side-menu.active {
        left: 0;
    }

    .side-menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 15px;
        color: var(--fragrancia-text);
    }

    .selected-block {
        overflow-y: auto;
        height: calc(100% - 50px);
    }

    .side-menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .side-menu-list li {
        margin-bottom: 12px;
    }

    /* ===== Cart Card (horizontal mini-card) ===== */
    .cart-card {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid var(--fragrancia-border);
        border-radius: 10px;
        padding: 8px;
        transition: box-shadow 0.2s;
    }

    .cart-card:hover {
        box-shadow: 0 2px 10px rgba(0,0,0,.06);
    }

    .cart-card__num {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--fragrancia-green);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        border-radius: 50%;
    }

    .cart-card__image {
        width: 60px;
        height: 60px;
        flex-shrink: 0;
        border-radius: 8px;
        overflow: hidden;
        background: #fafafa;
    }

    .cart-card__image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-card__info {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
    }

    .cart-card__name {
        flex: 1;
        font-size: 13px;
        font-weight: 600;
        color: var(--fragrancia-text);
        margin: 0;
        line-height: 1.3;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .cart-card__remove {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: #fee;
        color: #d9534f;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        transition: background 0.15s;
    }

    .cart-card__remove:hover {
        background: #fdd;
        color: #c9302c;
    }

    /* ===== Filter Panel ===== */
    .filter-overlay {
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

    .filter-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .filter-panel {
        position: fixed;
        top: 0;
        right: -360px;
        width: 340px;
        height: 100%;
        background: #fff;
        box-shadow: -2px 0 15px rgba(0,0,0,.2);
        transition: right 0.3s ease;
        z-index: 1050;
        display: flex;
        flex-direction: column;
    }

    .filter-panel.active {
        right: 0;
    }

    .filter-panel__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid var(--fragrancia-border);
    }

    .filter-panel__title {
        font-size: 20px;
        font-weight: 700;
        color: var(--fragrancia-text);
    }

    .filter-panel__close {
        background: none;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: var(--fragrancia-muted);
        line-height: 1;
    }

    .filter-panel__body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }

    .filter-group {
        margin-bottom: 24px;
    }

    .filter-group__label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--fragrancia-text);
        margin-bottom: 10px;
    }

    .filter-select--full {
        width: 100%;
    }

    .filter-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-chip {
        padding: 6px 16px;
        border: 1px solid var(--fragrancia-border);
        border-radius: 20px;
        background: #fff;
        font-size: 13px;
        color: var(--fragrancia-text);
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-chip:hover {
        border-color: var(--fragrancia-green);
        color: var(--fragrancia-green);
    }

    .filter-chip.active {
        background: var(--fragrancia-green);
        border-color: var(--fragrancia-green);
        color: #fff;
    }

    .filter-toggle {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        user-select: none;
    }

    .filter-toggle input {
        display: none;
    }

    .filter-toggle__slider {
        width: 44px;
        height: 24px;
        background: #ccc;
        border-radius: 12px;
        position: relative;
        transition: background 0.2s;
        flex-shrink: 0;
    }

    .filter-toggle__slider::after {
        content: '';
        width: 20px;
        height: 20px;
        background: #fff;
        border-radius: 50%;
        position: absolute;
        top: 2px;
        left: 2px;
        transition: transform 0.2s;
    }

    .filter-toggle input:checked + .filter-toggle__slider {
        background: var(--fragrancia-green);
    }

    .filter-toggle input:checked + .filter-toggle__slider::after {
        transform: translateX(20px);
    }

    .filter-toggle__text {
        font-size: 14px;
        color: var(--fragrancia-text);
    }

    .filter-panel__footer {
        padding: 16px 20px;
        border-top: 1px solid var(--fragrancia-border);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .filter-btn {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        border: 2px solid var(--fragrancia-green);
        transition: all 0.2s;
    }

    .filter-btn--apply {
        background: var(--fragrancia-green);
        color: #fff;
    }

    .filter-btn--apply:hover {
        background: var(--fragrancia-green-hover);
        border-color: var(--fragrancia-green-hover);
    }

    .filter-btn--reset {
        background: #fff;
        color: var(--fragrancia-green);
    }

    .filter-btn--reset:hover {
        background: #f0faf4;
    }

    @media (max-width: 768px) {
        .filter-panel {
            width: 300px;
            right: -320px;
        }
    }

    /* ===== Overlay ===== */
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

    /* ===== Pulse Animation ===== */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(47,173,102,.7), 0 0 8px rgba(47,173,102,.3);
            transform: scale(1);
        }
        40% {
            transform: scale(1.04);
        }
        70% {
            box-shadow: 0 0 0 18px rgba(47,173,102,0), 0 0 0 rgba(47,173,102,0);
            transform: scale(1);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(47,173,102,0), 0 0 8px rgba(47,173,102,.3);
            transform: scale(1);
        }
    }

    .btn-pulse {
        animation: pulse 1.4s infinite;
        position: relative;
    }

    .btn-pulse i {
        font-size: 18px;
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .product-card__image {
            width: 110px;
            min-height: 130px;
        }

        .product-card__name {
            font-size: 14px;
        }

        .search-bar__inner {
            flex-wrap: wrap;
        }

        .search-wrapper {
            width: 100%;
        }

        .search-bar__filters {
            width: 100%;
            justify-content: space-between;
        }

        .filter-select {
            flex: 1;
        }

        .catalog-container .row > .col-md-6 {
            width: 100%;
        }

        .side-menu {
            width: 280px;
            left: -280px;
        }

        .footer__inner {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }
    }
</style>
@endsection
