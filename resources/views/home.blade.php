@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="list-group list-group-flush">
                                <a href="{{url('home')}}" class="list-group-item list-group-item-action selected">Товары</a>
                                <a href="{{url('orders')}}" class="list-group-item list-group-item-action">Заказы</a>
                                <a href="{{url('brands')}}" class="list-group-item list-group-item-action">Бренды</a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h4>Товары</h4>
                            <hr class="my-4" style="border-top:1px solid #e0e0e0;">
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

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Для кого</label>
                                                    <select name="gender" class="form-control" id="productGender">
                                                        <option value="male" {{ old('gender', 'male') == 'male' ? 'selected' : '' }}>Мужской</option>
                                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Женский</option>
                                                        <option value="unisex" {{ old('gender') == 'unisex' ? 'selected' : '' }}>Унисекс</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Бренд</label>
                                                    <select name="brand_id" class="form-control" id="productBrand">
                                                        <option value="">-- Не выбран --</option>
                                                        @foreach($brands as $brand)
                                                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Качество</label>
                                                    <select name="quality" class="form-control" id="productQuality">
                                                        <option value="">-- Не выбрано --</option>
                                                        <option value="premium" {{ old('quality') == 'premium' ? 'selected' : '' }}>Премиум парфюм</option>
                                                        <option value="top" {{ old('quality') == 'top' ? 'selected' : '' }}>Топ парфюм</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-6 d-flex align-items-end">
                                                    <div class="custom-control custom-checkbox mb-2">
                                                        <input type="checkbox" class="custom-control-input" id="productIsNew" name="is_new" value="1" {{ old('is_new') ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="productIsNew">Новинка</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Картинка</label>
                                                <input type="file"
                                                       class="form-control-file"
                                                       accept="image/*"
                                                       id="productImageFile">
                                                <input type="hidden" name="cropped_image" id="croppedImageData">
                                                <small class="form-text text-muted">
                                                    Оставьте пустым, чтобы оставить текущее изображение
                                                </small>

                                                <div id="cropperContainer" class="mt-3" style="display:none;">
                                                    <div class="img-container" style="max-height:400px; overflow:hidden;">
                                                        <img id="cropperImage" src="" style="max-width:100%; display:block;">
                                                    </div>
                                                    <div class="btn-group btn-group-sm mt-2" role="group">
                                                        <button type="button" class="btn btn-outline-secondary" id="cropRotateLeft" title="Повернуть влево">
                                                            ↺ -90°
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary" id="cropRotateRight" title="Повернуть вправо">
                                                            ↻ +90°
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary" id="cropZoomIn" title="Увеличить">
                                                            🔍+
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary" id="cropZoomOut" title="Уменьшить">
                                                            🔍−
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary" id="cropReset" title="Сбросить">
                                                            ↩ Сброс
                                                        </button>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-info">Перетаскивайте рамку для обрезки. Используйте колёсико мыши для масштабирования.</small>
                                                    </div>
                                                </div>

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
                                                 alt="{{ $product->name }}">

                                            <div class="card-body d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <button class="btn btn-sm btn-outline-primary edit-product-btn"
                                                            data-toggle="modal"
                                                            data-target="#productModal"
                                                            data-product='@json($product)'>
                                                        ✏️ Редактировать
                                                    </button>

                                                    <!-- Крестик для удаления -->
                                                    <form method="POST"
                                                          action="{{ route('deleteProduct', $product->id) }}"
                                                          onsubmit="return confirm('Вы действительно хотите удалить этот товар?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">&times;</button>
                                                    </form>
                                                </div>

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

                                                @if($product->brand)
                                                    <p class="small"><strong>Бренд:</strong> {{ $product->brand->name }}</p>
                                                @endif

                                                @if($product->quality)
                                                    <p class="small">
                                                        <strong>Качество:</strong>
                                                        {{ $product->quality == 'premium' ? 'Премиум парфюм' : 'Топ парфюм' }}
                                                    </p>
                                                @endif

                                                @if($product->is_new)
                                                    <span class="badge badge-success">Новинка</span>
                                                @endif

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
                                    {{ $products->withQueryString()->links('pagination::bootstrap-4') }}
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
    let cropper = null;

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

    function updateKeywordsInput() {
        $('#keywordsInput').val(JSON.stringify(keywords));
    }

    function createTagElement(keyword) {
        return $('<div>', {
            class: 'breadcrumb-tag',
            html: `${keyword} <span class="close" data-keyword="${keyword}">&times;</span>`
        });
    }

    function addKeyword(keyword) {
        keyword = $.trim(keyword);
        if (keyword && $.inArray(keyword, keywords) === -1) {
            keywords.push(keyword);
            const tagElement = createTagElement(keyword);
            $('#tagsContainer').append(tagElement);
            updateKeywordsInput();
            tagElement.find('.close').click(function(e) {
                e.stopPropagation();
                removeKeyword($(this).data('keyword'));
            });
        }
    }

    function removeKeyword(keyword) {
        const index = $.inArray(keyword, keywords);
        if (index !== -1) {
            keywords.splice(index, 1);
            $(`[data-keyword="${keyword}"]`).parent().remove();
            updateKeywordsInput();
        }
    }

    function updateTagsDisplay() {
        $('#tagsContainer').empty();
        keywords.forEach(function(keyword) {
            const tagElement = createTagElement(keyword);
            $('#tagsContainer').append(tagElement);
            tagElement.find('.close').click(function(e) {
                e.stopPropagation();
                removeKeyword($(this).data('keyword'));
            });
        });
        updateKeywordsInput();
    }

    // --- Cropper.js ---
    function destroyCropper() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        $('#cropperContainer').hide();
        $('#croppedImageData').val('');
    }

    function initCropper(imageUrl) {
        destroyCropper();
        var $img = $('#cropperImage');
        $img.attr('src', imageUrl);
        $('#cropperContainer').show();

        $img.on('load', function() {
            $(this).off('load');
            cropper = new Cropper(this, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 0.9,
                responsive: true,
                restore: true,
                guides: true,
                center: true,
                highlight: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: true,
            });
        });

        if ($img[0].complete && $img[0].naturalWidth > 0) {
            $img.trigger('load');
        }
    }

    $('#productImageFile').on('change', function() {
        var files = this.files;
        if (!files || !files.length) {
            destroyCropper();
            return;
        }

        var file = files[0];
        var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Допустимы только изображения (JPEG, PNG, JPG, GIF, WEBP)');
            this.value = '';
            destroyCropper();
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            alert('Файл слишком большой. Максимальный размер: 10MB');
            this.value = '';
            destroyCropper();
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            initCropper(e.target.result);
        };
        reader.readAsDataURL(file);
    });

    $('#cropRotateLeft').on('click', function() { if (cropper) cropper.rotate(-90); });
    $('#cropRotateRight').on('click', function() { if (cropper) cropper.rotate(90); });
    $('#cropZoomIn').on('click', function() { if (cropper) cropper.zoom(0.1); });
    $('#cropZoomOut').on('click', function() { if (cropper) cropper.zoom(-0.1); });
    $('#cropReset').on('click', function() { if (cropper) cropper.reset(); });

    function getCroppedDataUrl() {
        if (!cropper) return null;
        var canvas = cropper.getCroppedCanvas({
            width: 800,
            height: 800,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        if (!canvas) return null;
        return canvas.toDataURL('image/jpeg', 0.9);
    }

    // --- Tags ---
    $('#tagInput').on('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addKeyword($(this).val());
            $(this).val('');
        }
    });

    $('#tagInput').on('blur', function() {
        if ($.trim($(this).val())) {
            addKeyword($(this).val());
            $(this).val('');
        }
    });

    $('#tagsWrapper').on('click', function(e) {
        if (e.target === this || e.target === $('#tagsContainer')[0]) {
            $('#tagInput').focus();
        }
    });

    // --- Edit product ---
    $(document).on('click', '.edit-product-btn', function() {
        const product = $(this).data('product');
        if (!product) return;

        destroyCropper();
        $('#productImageFile').val('');

        $('#productId').val(product.id);
        $('#productName').val(product.name);
        $('#productSku').val(product.article || '');
        $('#productGender').val(product.gender);
        $('#productBrand').val(product.brand_id || '');
        $('#productQuality').val(product.quality || '');
        $('#productIsNew').prop('checked', !!product.is_new);
        $('#modalTitle').text('Редактировать товар');

        const currentImageDiv = $('#currentImage');
        if (product.image) {
            currentImageDiv.html(
                '<strong>Текущее изображение:</strong><br>' +
                '<img src="' + product.image + '" class="img-thumbnail mt-1" style="max-height:100px;">'
            );
        } else {
            currentImageDiv.html('<strong>Нет изображения</strong>');
        }

        keywords = product.keywords ? product.keywords.map(k => k.name) : [];
        updateTagsDisplay();

        setTimeout(() => { $('#tagInput').focus(); }, 500);
    });

    // --- Add product ---
    $('#addProductBtn').click(function() {
        destroyCropper();
        $('#productImageFile').val('');

        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#productName').val('');
        $('#productSku').val('');
        $('#productBrand').val('');
        $('#productQuality').val('');
        $('#productIsNew').prop('checked', false);
        $('#modalTitle').text('Добавить товар');
        $('#currentImage').empty();

        keywords = [];
        updateTagsDisplay();

        setTimeout(() => { $('#productName').focus(); }, 500);
    });

    // --- Close modal ---
    $('#productModal').on('hidden.bs.modal', function() {
        destroyCropper();
        $('#productImageFile').val('');
        if (!$('#productId').val()) {
            keywords = [];
            $('#tagsContainer').empty();
            $('#keywordsInput').val('[]');
            $('#tagInput').val('');
        }
    });

    // --- Form submit ---
    $('#productForm').on('submit', function(e) {
        e.preventDefault();

        if (!$('#productName').val().trim()) {
            alert('Пожалуйста, введите название товара');
            $('#productName').focus();
            return false;
        }

        var submitBtn = $('#submitBtn');
        submitBtn.html('<span class="spinner-border spinner-border-sm"></span> Сохранение...');
        submitBtn.prop('disabled', true);

        if (cropper) {
            var dataUrl = getCroppedDataUrl();
            $('#croppedImageData').val(dataUrl);
        }

        var formData = new FormData(this);

        formData.delete('image');
        var fileInput = $('#productImageFile')[0];
        if (!cropper && fileInput && fileInput.files.length > 0) {
            formData.append('image', fileInput.files[0]);
        }

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                window.location.reload();
            },
            error: function(xhr) {
                submitBtn.html('Сохранить');
                submitBtn.prop('disabled', false);

                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var messages = [];
                    for (var field in errors) {
                        messages.push(errors[field].join(', '));
                    }
                    alert('Ошибки валидации:\n' + messages.join('\n'));
                } else {
                    alert('Ошибка при сохранении товара');
                }
            }
        });
    });

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
.img-container {
    max-height: 400px;
    background: #f0f0f0;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    overflow: hidden;
}
.img-container img {
    display: block;
    max-width: 100%;
}
#cropperContainer .btn-group .btn {
    font-size: 12px;
}
.card-img-top {
    width: 100%;
    height: 220px;
    object-fit: cover;
    object-position: center;
}
</style>
@endsection
