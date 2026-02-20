@extends('../layouts/template')

@section('content')
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
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let page = 1;
let loading = false;
let lastPage = false;
let suggestTimer = null;
let productTimer = null;

let activeFilters = {
    gender: '',
    brand_id: '',
    quality: '',
    is_new: ''
};

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

$('#openFilter').on('click', function() {
    $('#filterPanel').addClass('active');
    $('#filterOverlay').addClass('active');
});

$('#closeFilter, #filterOverlay').on('click', function() {
    $('#filterPanel').removeClass('active');
    $('#filterOverlay').removeClass('active');
});

$('.filter-chips').on('click', '.filter-chip', function() {
    $(this).toggleClass('active');
});

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
            locale: document.documentElement.lang,
            homePage: 1
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
        padding-bottom: 40px;
    }

    /* ===== Product Card ===== */
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

    .btn-kaspi {
        background: #f14635;
    }

    .btn-kaspi:hover {
        background: #d93c2d;
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
        padding: 10px 14px;
        border: 1px solid var(--fragrancia-border);
        border-radius: 8px;
        font-size: 14px;
        background: #fff;
        cursor: pointer;
        outline: none;
    }

    .filter-select--full:focus {
        border-color: var(--fragrancia-green);
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

        .filter-panel {
            width: 300px;
            right: -320px;
        }
    }
</style>
@endsection
