<div class="list-group list-group-flush">
    <a href="{{url('home')}}" class="list-group-item list-group-item-action {{ ($active ?? '') === 'home' ? 'selected' : '' }}">Товары</a>
    <a href="{{url('orders')}}" class="list-group-item list-group-item-action {{ ($active ?? '') === 'orders' ? 'selected' : '' }}">Заказы</a>
    <a href="{{url('brands')}}" class="list-group-item list-group-item-action {{ ($active ?? '') === 'brands' ? 'selected' : '' }}">Бренды</a>
    <a href="{{route('broadcasts')}}" class="list-group-item list-group-item-action {{ ($active ?? '') === 'broadcasts' ? 'selected' : '' }}">Рассылки</a>
    <a href="{{route('message-templates.index')}}" class="list-group-item list-group-item-action {{ ($active ?? '') === 'templates' ? 'selected' : '' }}">Шаблоны сообщений</a>
    <a href="{{route('kaspi-status.index')}}" class="list-group-item list-group-item-action {{ ($active ?? '') === 'kaspi-status' ? 'selected' : '' }}">Статус Kaspi</a>
</div>
