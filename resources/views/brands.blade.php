@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            @include('partials.admin-sidebar', ['active' => 'brands'])
                        </div>
                        <div class="col-md-9">
                            <h4>Бренды</h4>
                            <hr class="my-4" style="border-top:1px solid #e0e0e0;">

                            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#brandModal" id="addBrandBtn">
                                Добавить бренд
                            </button>

                            <div class="modal fade" id="brandModal" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <form id="brandForm" action="{{ route('saveBrand') }}" method="POST" class="modal-content">
                                        @csrf
                                        <input type="hidden" name="brand_id" id="brandId">

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="brandModalTitle">Добавить бренд</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Название бренда</label>
                                                <input type="text" name="name" class="form-control" required id="brandName" value="{{ old('name') }}">
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                            <button type="submit" class="btn btn-success">Сохранить</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('brands') }}" class="mb-4">
                                <div class="form-row">
                                    <div class="col-md-8">
                                        <input type="text" name="name" class="form-control" placeholder="Поиск по названию" value="{{ request('name') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-dark btn-block">Найти</button>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Название</th>
                                        <th>Кол-во товаров</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($brands as $brand)
                                        <tr>
                                            <td>{{ $brand->id }}</td>
                                            <td>{{ $brand->name }}</td>
                                            <td>{{ $brand->products_count }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-brand-btn"
                                                        data-toggle="modal"
                                                        data-target="#brandModal"
                                                        data-id="{{ $brand->id }}"
                                                        data-name="{{ $brand->name }}">
                                                    Редактировать
                                                </button>
                                                <form method="POST" action="{{ route('deleteBrand', $brand->id) }}" class="d-inline"
                                                      onsubmit="return confirm('Удалить бренд?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if($brands->hasPages())
                                <div class="mt-4">
                                    {{ $brands->withQueryString()->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#addBrandBtn').click(function() {
        $('#brandId').val('');
        $('#brandName').val('');
        $('#brandModalTitle').text('Добавить бренд');
    });

    $(document).on('click', '.edit-brand-btn', function() {
        $('#brandId').val($(this).data('id'));
        $('#brandName').val($(this).data('name'));
        $('#brandModalTitle').text('Редактировать бренд');
    });

    $('#brandModal').on('hidden.bs.modal', function() {
        $('#brandId').val('');
        $('#brandName').val('');
    });
});
</script>
@endsection
