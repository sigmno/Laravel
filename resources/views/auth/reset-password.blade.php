@extends('layouts.app')

@section('title', 'Новый пароль')

@section('content')
    <div class="auth-wrap">
        <section class="form-card">
            <div class="page-title">
                <h1>Новый пароль</h1>
                <p>Введите новый пароль для вашего аккаунта.</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="grid">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus>
                    @error('email') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password">Новый пароль</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password">
                    @error('password') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Повторите пароль</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
                    @error('password_confirmation') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <button class="btn btn-primary" type="submit">Сохранить пароль</button>
            </form>
        </section>
    </div>
@endsection
