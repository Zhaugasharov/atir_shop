@extends('../layouts/template')

@section('content')
<div class="container text-center my-5">

    {{-- Центральная коробка --}}
    <div class="mb-5">
        <img src="https://cdn.pixabay.com/photo/2017/01/31/17/51/box-2029808_1280.png" alt="Коробка">

    </div>

    {{-- Три плюса под коробкой --}}
    <div class="d-flex justify-content-center gap-4">
        @for ($i = 1; $i <= 3; $i++)
        <div class="aroma-slot position-relative" data-slot="{{ $i }}">
            <button class="btn btn-outline-primary rounded-0 plus-btn" data-bs-toggle="modal" data-bs-target="#aromaModal{{ $i }}">
                <span style="font-size: 2rem;">+</span>
            </button>
        </div>
        @endfor
    </div>

    {{-- Кнопка "Выбрать" внизу --}}
    <div class="mt-4">
        <button id="finalSelectBtn" class="btn btn-success" disabled>Выбрать</button>
    </div>
</div>

{{-- Модальные окна для каждого плюса --}}
@for ($i = 1; $i <= 3; $i++)
<div class="modal fade" id="aromaModal{{ $i }}" tabindex="-1" aria-labelledby="aromaModalLabel{{ $i }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Выберите аромат</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">

                {{-- Типы ароматов --}}
                <div class="mb-3 d-flex justify-content-center gap-2">
                    <button class="btn btn-outline-secondary type-btn active" data-type="all">Все</button>
                    <button class="btn btn-outline-secondary type-btn" data-type="female">Женский</button>
                    <button class="btn btn-outline-secondary type-btn" data-type="male">Мужской</button>
                    <button class="btn btn-outline-secondary type-btn" data-type="unisex">Унисекс</button>
                </div>

                {{-- Поиск --}}
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Поиск по названию..." id="searchInput{{ $i }}">
                </div>

                {{-- Список ароматов --}}
                <div class="row" id="aromaList{{ $i }}">
                    {{-- Пример ароматов --}}
                    @php
                        $aromas = [
                            ['name'=>'Роза','type'=>'female','img'=>'rose.jpg'],
                            ['name'=>'Лаванда','type'=>'unisex','img'=>'lavender.jpg'],
                            ['name'=>'Сандал','type'=>'male','img'=>'sandal.jpg'],
                            ['name'=>'Ваниль','type'=>'female','img'=>'vanilla.jpg'],
                            ['name'=>'Мята','type'=>'unisex','img'=>'mint.jpg'],
                        ];
                    @endphp
                    @foreach ($aromas as $aroma)
                    <div class="col-md-4 mb-3 aroma-item" data-name="{{ $aroma['name'] }}" data-type="{{ $aroma['type'] }}">
                        <div class="card aroma-card h-100 cursor-pointer">
                            <img src="{{ asset('images/'.$aroma['img']) }}" class="card-img-top" alt="{{ $aroma['name'] }}">
                            <div class="card-body text-center">
                                <h6 class="card-title">{{ $aroma['name'] }}</h6>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary select-aroma-btn" data-slot="{{ $i }}" disabled>Выбрать</button>
            </div>
        </div>
    </div>
</div>
@endfor

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const slots = [1,2,3];
    const selectedAromas = {};

    slots.forEach(slot => {
        const modal = document.getElementById(`aromaModal${slot}`);
        const aromaList = document.getElementById(`aromaList${slot}`);
        const searchInput = document.getElementById(`searchInput${slot}`);
        const selectBtn = modal.querySelector('.select-aroma-btn');

        // Выбор аромата
        aromaList.querySelectorAll('.aroma-card').forEach(card => {
            card.addEventListener('click', () => {
                // Снять выделение у всех
                aromaList.querySelectorAll('.aroma-card').forEach(c => c.classList.remove('border-primary', 'border-3'));
                // Выделить выбранный
                card.classList.add('border-primary', 'border-3');
                selectBtn.disabled = false;
                selectBtn.dataset.selectedImg = card.querySelector('img').src;
                selectBtn.dataset.selectedName = card.querySelector('h6').textContent;
            });
        });

        // Сортировка по типу
        modal.querySelectorAll('.type-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                modal.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const type = btn.dataset.type;
                aromaList.querySelectorAll('.aroma-item').forEach(item => {
                    item.style.display = (type === 'all' || item.dataset.type === type) ? 'block' : 'none';
                });
            });
        });

        // Поиск
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase();
            aromaList.querySelectorAll('.aroma-item').forEach(item => {
                item.style.display = item.dataset.name.toLowerCase().includes(query) ? 'block' : 'none';
            });
        });

        // Подтверждение выбора
        selectBtn.addEventListener('click', () => {
            const slotBtn = document.querySelector(`.aroma-slot[data-slot="${slot}"] .plus-btn`);
            slotBtn.innerHTML = `<img src="${selectBtn.dataset.selectedImg}" class="img-fluid" style="width:50px;">`;
            selectedAromas[slot] = selectBtn.dataset.selectedName;
            selectBtn.disabled = true;
            bootstrap.Modal.getInstance(modal).hide();

            // Включаем кнопку "Выбрать", если все слоты выбраны
            const finalBtn = document.getElementById('finalSelectBtn');
            finalBtn.disabled = Object.keys(selectedAromas).length < 3;
        });
    });

    // Кнопка финального выбора
    document.getElementById('finalSelectBtn').addEventListener('click', () => {
        alert('Вы выбрали ароматы: ' + Object.values(selectedAromas).join(', '));
    });
});
</script>
@endsection

