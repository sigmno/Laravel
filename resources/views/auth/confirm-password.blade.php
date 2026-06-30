@extends('layouts.app')

@section('title', 'Подтверждение пароля')

@section('content')
    <div class="auth-wrap">
        <section class="form-card">
            <div class="page-title">
                <h1>Подтверждение пароля</h1>
                <p>Для безопасности подтвердите пароль перед продолжением.</p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="grid">
                @csrf
                <div class="field">
                    <label for="password">Пароль</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password">
                    @error('password') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <button class="btn btn-primary" type="submit">Подтвердить</button>
            </form>
        </section>
    </div>
@endsection
