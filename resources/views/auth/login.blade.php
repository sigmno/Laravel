@extends('layouts.app')

@section('title', 'Вход')

@section('content')
    <div class="auth-wrap">
        <section class="form-card">
            <div class="page-title">
                <h1>Вход в библиотеку</h1>
                <p>Введите email и пароль, чтобы управлять бронированиями.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="grid">
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password">Пароль</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password">
                    @error('password') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <label class="checkbox-row">
                    <input type="checkbox" name="remember">
                    <span>Запомнить меня</span>
                </label>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Войти</button>
                    <a class="btn btn-outline" href="{{ route('register') }}">Создать аккаунт</a>
                    <a class="nav-link" style="color: var(--blue-700);" href="{{ route('password.request') }}">Забыли пароль?</a>
                </div>
            </form>
        </section>
    </div>
@endsection
