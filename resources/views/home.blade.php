@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item list-group-item-action selected">Товары</a>
                                <a href="{{url('orders')}}" class="list-group-item list-group-item-action">Заказы</a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <a href="{{asset('example.xlsx')}}">Пример файла</a>
                            <form action="{{route('saveProducts')}}" method="POST" enctype="multipart/form-data" class="d-inline">
                                @csrf
                                <input type="file" name="excel" id="excelFile" accept=".xls,.xlsx" hidden onchange="this.form.submit()">
                                <button type="button" class="btn btn-success"
                                        onclick="document.getElementById('excelFile').click()">
                                    Загрузить товары
                                </button>
                            </form>

                            <button class="btn btn-primary" data-toggle="modal" data-target="#productModal" id="addProductBtn">
                                Загрузить товар
                            </button>

                            <hr class="my-4" style="border-top:1px solid #e0e0e0;">

                            <!-- Модальное окно для редактирования/добавления -->
                            <div class="modal fade" id="productModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <form id="productForm"
                                          action="{{ route('saveProduct') }}"
                                          method="POST"
                                          enctype="multipart/form-data"
                                          class="modal-content">
                                        @csrf
                                        <input type="hidden" name="product_id" id="productId">

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalTitle">Добавить товар</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Название товара</label>
                                                    <input type="text"
                                                           name="name"
                                                           class="form-control"
                                                           required
                                                           value="{{ old('name') }}"
                                                           id="productName">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>Артикул</label>
                                                    <input type="text"
                                                           name="sku"
                                                           class="form-control"
                                                           value="{{ old('sku') }}"
                                                           id="productSku">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Для кого</label>
                                                <select name="gender" class="form-control" id="productGender">
                                                    <option value="male" {{ old('gender', 'male') == 'male' ? 'selected' : '' }}>Мужской</option>
                                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Женский</option>
                                                    <option value="unisex" {{ old('gender') == 'unisex' ? 'selected' : '' }}>Унисекс</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Картинка</label>
                                                <input type="file"
                                                       name="image"
                                                       class="form-control-file"
                                                       accept="image/*"
                                                       id="productImage">
                                                <small class="form-text text-muted">
                                                    Оставьте пустым, чтобы оставить текущее изображение
                                                </small>
                                                <div id="currentImage" class="mt-2"></div>
                                            </div>

                                            <div class="form-group">
                                                <label>Тип аромата / ключевые слова</label>

                                                <div id="tagsWrapper"
                                                     class="border rounded p-2 d-flex flex-wrap align-items-center"
                                                     style="min-height:42px; cursor:text">

                                                    <div id="tagsContainer" class="d-flex flex-wrap align-items-center"></div>

                                                    <input type="text"
                                                           id="tagInput"
                                                           class="border-0 flex-grow-1 ml-1"
                                                           placeholder="Введите слово и нажмите Enter"
                                                           style="outline:none; min-width: 200px;">
                                                </div>

                                                <input type="hidden" name="keywords" id="keywordsInput" value="{{ old('keywords', '[]') }}">
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                Отмена
                                            </button>
                                            <button type="submit" class="btn btn-success" id="submitBtn">
                                                Сохранить
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('home') }}" class="mb-4">
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <input type="text"
                                               name="name"
                                               class="form-control"
                                               placeholder="Название товара"
                                               value="{{ request('name') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <select name="gender" class="form-control">
                                            <option value="">Все</option>
                                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Мужской</option>
                                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Женский</option>
                                            <option value="unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>Унисекс</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <input type="text"
                                               name="keywords"
                                               class="form-control"
                                               placeholder="Ключевые слова"
                                               value="{{ request('keywords') }}">
                                    </div>

                                    <div class="col-md-2">
                                        <button class="btn btn-dark btn-block">Найти</button>
                                    </div>
                                </div>
                            </form>

                            <div class="row">
                                @foreach($products as $product)
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            <img src="{{ $product->image_url }}"
                                                 class="card-img-top"
                                                 style="height:220px; object-fit:cover"
                                                 alt="{{ $product->name }}">

                                            <div class="card-body">
                                                <button class="btn btn-sm btn-outline-primary mb-3 edit-product-btn"
                                                        data-toggle="modal"
                                                        data-target="#productModal"
                                                        data-product='@json($product)'>
                                                    ✏️ Редактировать
                                                </button>

                                                <h5 class="card-title">{{ $product->name }}</h5>

                                                <p class="small">
                                                    <strong>Артикул:</strong> {{ $product->article ?? 'не указан' }}
                                                </p>

                                                <p class="small">
                                                    <strong>Для кого:</strong>
                                                    @if($product->gender == 'male')
                                                        Мужской
                                                    @elseif($product->gender == 'female')
                                                        Женский
                                                    @else
                                                        Унисекс
                                                    @endif
                                                </p>

                                                @if($product->keywords->count() > 0)
                                                    <div class="mb-2">
                                                        <strong>Ключевые слова:</strong>
                                                        <div class="mt-1">
                                                            @foreach($product->keywords as $keyword)
                                                                <span class="badge badge-secondary mr-1 mb-1">
                                                                    {{ $keyword->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($products->hasPages())
                                <div class="mt-4">
                                    {{ $products->withQueryString()->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let keywords = [];

    // Инициализация
    function initKeywords() {
        const keywordsInput = $('#keywordsInput');
        if (keywordsInput.val()) {
            try {
                const savedKeywords = JSON.parse(keywordsInput.val());
                if ($.isArray(savedKeywords)) {
                    keywords = savedKeywords;
                    updateTagsDisplay();
                }
            } catch (e) {
                console.error('Error parsing keywords:', e);
            }
        }
    }

    // Обновление скрытого поля
    function updateKeywordsInput() {
        $('#keywordsInput').val(JSON.stringify(keywords));
    }

    // Создание элемента тега
    function createTagElement(keyword) {
        return $('<div>', {
            class: 'breadcrumb-tag',
            html: `${keyword} <span class="close" data-keyword="${keyword}">&times;</span>`
        });
    }

    // Добавление ключевого слова
    function addKeyword(keyword) {
        keyword = $.trim(keyword);

        if (keyword && $.inArray(keyword, keywords) === -1) {
            keywords.push(keyword);
            const tagElement = createTagElement(keyword);
            $('#tagsContainer').append(tagElement);
            updateKeywordsInput();

            // Добавляем обработчик удаления
            tagElement.find('.close').click(function(e) {
                e.stopPropagation();
                const keywordToRemove = $(this).data('keyword');
                removeKeyword(keywordToRemove);
            });
        }
    }

    // Удаление ключевого слова
    function removeKeyword(keyword) {
        const index = $.inArray(keyword, keywords);
        if (index !== -1) {
            keywords.splice(index, 1);
            $(`[data-keyword="${keyword}"]`).parent().remove();
            updateKeywordsInput();
        }
    }

    // Обновление отображения тегов
    function updateTagsDisplay() {
        $('#tagsContainer').empty();
        keywords.forEach(function(keyword) {
            const tagElement = createTagElement(keyword);
            $('#tagsContainer').append(tagElement);

            // Добавляем обработчик удаления
            tagElement.find('.close').click(function(e) {
                e.stopPropagation();
                const keywordToRemove = $(this).data('keyword');
                removeKeyword(keywordToRemove);
            });
        });
        updateKeywordsInput();
    }

    // Обработчик ввода тегов
    $('#tagInput').on('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addKeyword($(this).val());
            $(this).val('');
        }
    });

    // Обработчик blur для тегов
    $('#tagInput').on('blur', function() {
        if ($.trim($(this).val())) {
            addKeyword($(this).val());
            $(this).val('');
        }
    });

    // Фокус на поле ввода тегов при клике на контейнер
    $('#tagsWrapper').on('click', function(e) {
        if (e.target === this || e.target === $('#tagsContainer')[0]) {
            $('#tagInput').focus();
        }
    });

    // Редактирование продукта
    $(document).on('click', '.edit-product-btn', function() {
        const product = $(this).data('product');
        if (!product) return;

        $('#productId').val(product.id);
        $('#productName').val(product.name);
        $('#productSku').val(product.article || '');
        $('#productGender').val(product.gender);
        $('#modalTitle').text('Редактировать товар');

        // Текущее изображение
        const currentImageDiv = $('#currentImage');
        if (product.image) {
            currentImageDiv.html(`
                <strong>Текущее изображение:</strong><br>
                <img src="${product.image}"
                     class="img-thumbnail mt-1"
                     style="max-height: 100px;">
            `);
        } else {
            currentImageDiv.html('<strong>Нет изображения</strong>');
        }

        // Ключевые слова
        keywords = product.keywords ? product.keywords.map(k => k.name) : [];
        updateTagsDisplay();

        // Фокус на поле ввода
        setTimeout(() => {
            $('#tagInput').focus();
        }, 500);
    });

    // Добавление нового продукта
    $('#addProductBtn').click(function() {
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#productName').val('');
        $('#productSku').val('');
        $('#modalTitle').text('Добавить товар');
        $('#currentImage').empty();

        keywords = [];
        updateTagsDisplay();

        setTimeout(() => {
            $('#productName').focus();
        }, 500);
    });

    // Очистка формы при закрытии модального окна
    $('#productModal').on('hidden.bs.modal', function() {
        // Очищаем только если это не редактирование
        if (!$('#productId').val()) {
            keywords = [];
            $('#tagsContainer').empty();
            $('#keywordsInput').val('[]');
            $('#tagInput').val('');
        }
    });

    // Обработка отправки формы
    $('#productForm').on('submit', function(e) {
        // Простая валидация
        if (!$('#productName').val().trim()) {
            e.preventDefault();
            alert('Пожалуйста, введите название товара');
            $('#productName').focus();
            return false;
        }

        // Проверка файла (если выбран)
        const fileInput = $('#productImage')[0];
        if (fileInput && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (file.size > maxSize) {
                e.preventDefault();
                alert('Файл слишком большой. Максимальный размер: 2MB');
                return false;
            }

            // Проверка типа файла
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                e.preventDefault();
                alert('Допустимы только изображения (JPEG, PNG, JPG, GIF)');
                return false;
            }
        }

        // Показываем индикатор загрузки
        const submitBtn = $('#submitBtn');
        submitBtn.html('<span class="spinner-border spinner-border-sm"></span> Сохранение...');
        submitBtn.prop('disabled', true);

        // Форма отправится нормально
    });

    // Инициализация ключевых слов при загрузке
    initKeywords();
});
</script>
@endsection

@section('style')
<style>
.breadcrumb-tag {
    display: inline-flex;
    align-items: center;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 2px 8px;
    margin: 2px;
    font-size: 14px;
}
.breadcrumb-tag .close {
    font-size: 16px;
    margin-left: 5px;
    cursor: pointer;
    opacity: 0.6;
}
.breadcrumb-tag .close:hover {
    opacity: 1;
}
</style>
@endsection
