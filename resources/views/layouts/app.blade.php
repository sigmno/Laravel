<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Электронная библиотека')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="site-header">
    <div class="container nav">
        <a class="brand" href="{{ route('catalog.index') }}">
            <span class="brand-mark">Б</span>
            <span>Электронная библиотека</span>
        </a>

        <nav class="nav-links">
            <a class="nav-link" href="{{ route('catalog.index') }}">Каталог</a>
            @auth
                <a class="nav-link" href="{{ route('dashboard') }}">Кабинет</a>
                <a class="nav-link" href="{{ route('reservations.index') }}">Мои бронирования</a>
                @if(auth()->user()->isAdmin())
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">Админ-панель</a>
                @endif
                <a class="nav-link" href="{{ route('profile.edit') }}">Профиль</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-primary" type="submit">Выйти</button>
                </form>
            @else
                <a class="nav-link" href="{{ route('login') }}">Вход</a>
                <a class="btn btn-primary" href="{{ route('register') }}">Регистрация</a>
            @endauth
        </nav>
    </div>
</header>

<main class="page">
    <div class="container">
        @include('partials.flash')
        @yield('content')
    </div>
</main>
</body>
</html>
