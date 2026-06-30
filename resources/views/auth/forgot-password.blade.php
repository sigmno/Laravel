@extends('layouts.app')

@section('title', 'Восстановление пароля')

@section('content')
    <div class="auth-wrap">
        <section class="form-card">
            <div class="page-title">
                <h1>Восстановление пароля</h1>
                <p>Укажите email, и система отправит ссылку для сброса пароля в лог Laravel.</p>
            </div>

            <form method="POST" action="{{ route('password.email') }}" class="grid">
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                    @error('email') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Отправить ссылку</button>
                    <a class="btn btn-outline" href="{{ route('login') }}">Назад ко входу</a>
                </div>
            </form>
        </section>
    </div>
@endsection
