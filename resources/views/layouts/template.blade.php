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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        {{-- Your custom CSS --}}
        <style>
            .cursor-pointer { cursor: pointer; }

            .navbar { padding: 8px 0; }
            .navbar-brand img {
                width: 150px;
                height: 150px;
                transition: all 0.2s;
            }

            .lang-btn {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: none;
                border: none;
                padding: 6px 10px;
                font-size: 14px;
                font-weight: 500;
                color: #333;
                cursor: pointer;
                border-radius: 6px;
                transition: background 0.15s;
            }

            .lang-btn:hover,
            .lang-btn:focus {
                background: #f0f0f0;
                outline: none;
                box-shadow: none;
            }

            .lang-btn::after {
                font-size: 10px;
            }

            .lang-flag {
                width: 22px;
                height: 16px;
                object-fit: cover;
                border-radius: 2px;
                vertical-align: middle;
                box-shadow: 0 0 1px rgba(0,0,0,.2);
            }

            .lang-item {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                font-size: 14px;
            }

            .lang-item:hover {
                background: #f8f8f8;
            }

            @media (max-width: 768px) {
                .navbar-brand img {
                    width: 60px;
                    height: 60px;
                }
                .navbar { padding: 4px 0; }
            }
        </style>

        @yield('style')

    </head>
    <body>
        <div id="app">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{asset('logo.png')}}" width="150" height="150" alt="">
                    </a>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <div class="dropdown">
                                <button class="lang-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    @if(app()->getLocale() === 'ru')
                                        <img class="lang-flag" src="https://flagcdn.com/w40/ru.png" alt="RU"> RU
                                    @elseif(app()->getLocale() === 'en')
                                        <img class="lang-flag" src="https://flagcdn.com/w40/gb.png" alt="EN"> EN
                                    @else
                                        <img class="lang-flag" src="https://flagcdn.com/w40/kz.png" alt="KK"> KK
                                    @endif
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item lang-item" href="{{ route('lang.switch', 'ru') }}"><img class="lang-flag" src="https://flagcdn.com/w40/ru.png" alt="RU"> Русский</a></li>
                                    <li><a class="dropdown-item lang-item" href="{{ route('lang.switch', 'en') }}"><img class="lang-flag" src="https://flagcdn.com/w40/gb.png" alt="EN"> English</a></li>
                                    <li><a class="dropdown-item lang-item" href="{{ route('lang.switch', 'kz') }}"><img class="lang-flag" src="https://flagcdn.com/w40/kz.png" alt="KK"> Қазақша</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>

                </div>
            </nav>
            @yield('content')
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" ></script>

        {{-- Optional: jQuery (если нужны плагины) --}}
        <script src="https://code.jquery.com/jquery-3.6.4.min.js" ></script>

        {{-- Скрипты страницы --}}
        @yield('scripts')
    </body>
</html>
