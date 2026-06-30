@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
    <section class="page-title">
        <h1>Профиль</h1>
        <p>Управление именем, email и паролем аккаунта.</p>
    </section>

    <div class="grid two-columns">
        <section class="form-card">
            <h2>Данные профиля</h2>
            <form method="POST" action="{{ route('profile.update') }}" class="grid">
                @csrf
                @method('PATCH')

                <div class="field">
                    <label for="name">Имя</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <button class="btn btn-primary" type="submit">Сохранить</button>
            </form>
        </section>

        <section class="form-card">
            <h2>Смена пароля</h2>
            <form method="POST" action="{{ route('password.update') }}" class="grid">
                @csrf
                @method('PUT')

                <div class="field">
                    <label for="current_password">Текущий пароль</label>
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password">
                    @error('current_password', 'updatePassword') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password">Новый пароль</label>
                    <input id="password" name="password" type="password" autocomplete="new-password">
                    @error('password', 'updatePassword') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Повторите пароль</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
                </div>

                <button class="btn btn-secondary" type="submit">Обновить пароль</button>
            </form>
        </section>
    </div>

    <section class="form-card" style="margin-top: 20px;">
        <h2>Удаление аккаунта</h2>
        <p class="muted">После удаления аккаунта личные данные будут удалены. Бронирования удалятся вместе с пользователем.</p>
        <form method="POST" action="{{ route('profile.destroy') }}" class="grid" style="max-width: 520px;">
            @csrf
            @method('DELETE')
            <div class="field">
                <label for="delete_password">Пароль</label>
                <input id="delete_password" name="password" type="password">
                @error('password', 'userDeletion') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            <button class="btn btn-danger" type="submit">Удалить аккаунт</button>
        </form>
    </section>
@endsection
