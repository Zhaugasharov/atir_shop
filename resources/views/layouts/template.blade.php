<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Выбор аромата')</title>

        {{-- Bootstrap CSS CDN --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        {{-- Optionally: Bootstrap Icons --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

        {{-- Your custom CSS --}}
        <style>
            .cursor-pointer { cursor: pointer; }
        </style>
    </head>
    <body>

        @yield('content')

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" ></script>

        {{-- Optional: jQuery (если нужны плагины) --}}
        <script src="https://code.jquery.com/jquery-3.6.4.min.js" ></script>

        {{-- Скрипты страницы --}}
        @yield('scripts')
    </body>
</html>
