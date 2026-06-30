@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
    <div class="auth-wrap">
        <section class="form-card">
            <div class="page-title">
                <h1>Регистрация читателя</h1>
                <p>Первый зарегистрированный пользователь автоматически станет администратором проекта.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="grid">
                @csrf
                <div class="field">
                    <label for="name">Имя</label>
                    <input id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                    @error('name') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username">
                    @error('email') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password">Пароль</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password">
                    @error('password') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Повторите пароль</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
                    @error('password_confirmation') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Зарегистрироваться</button>
                    <a class="btn btn-outline" href="{{ route('login') }}">Уже есть аккаунт</a>
                </div>
            </form>
        </section>
    </div>
@endsection
