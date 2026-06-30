@extends('layouts.app')

@section('title', $book->title)

@section('content')
    <section class="split">
        <div class="card">
            <div class="book-cover" style="height: 420px;">
                @if($book->image)
                    <img src="{{ $book->image }}" alt="{{ $book->title }}">
                @else
                    <span>{{ $book->title }}</span>
                @endif
            </div>
        </div>

        <div class="form-card">
            <div class="page-title">
                <h1>{{ $book->title }}</h1>
                <p>{{ $book->author->name }} • {{ $book->genre->name }}</p>
            </div>

            <p>{{ $book->description ?: 'Описание книги пока не добавлено.' }}</p>

            <div class="grid two-columns" style="margin-top: 18px;">
                <div>
                    <strong>Год издания</strong>
                    <p class="meta">{{ $book->year ?: 'не указан' }}</p>
                </div>
                <div>
                    <strong>ISBN</strong>
                    <p class="meta">{{ $book->isbn ?: 'не указан' }}</p>
                </div>
                <div>
                    <strong>Всего экземпляров</strong>
                    <p class="meta">{{ $book->total_copies }}</p>
                </div>
                <div>
                    <strong>Доступно</strong>
                    <p class="meta">{{ $book->available_copies }}</p>
                </div>
            </div>

            <div class="actions">
                <a class="btn btn-outline" href="{{ route('catalog.index') }}">Назад в каталог</a>
                @auth
                    @if($hasActiveReservation)
                        <button class="btn btn-muted" type="button">Уже забронировано</button>
                    @elseif($book->is_available)
                        <form method="POST" action="{{ route('reservations.store', $book) }}">
                            @csrf
                            <button class="btn btn-primary" type="submit">Забронировать</button>
                        </form>
                    @else
                        <button class="btn btn-muted" type="button">Нет доступных экземпляров</button>
                    @endif
                @else
                    <a class="btn btn-primary" href="{{ route('login') }}">Войти для бронирования</a>
                @endauth
            </div>
        </div>
    </section>
@endsection
