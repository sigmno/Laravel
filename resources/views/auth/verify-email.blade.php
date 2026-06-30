@extends('layouts.app')

@section('title', 'Подтверждение email')

@section('content')
    <div class="auth-wrap">
        <section class="form-card">
            <div class="page-title">
                <h1>Подтверждение email</h1>
                <p>Перед началом работы подтвердите email по ссылке из письма.</p>
            </div>

            <div class="actions">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button class="btn btn-primary" type="submit">Отправить письмо повторно</button>
                </form>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline" type="submit">Выйти</button>
                </form>
            </div>
        </section>
    </div>
@endsection
