@extends('layouts.app')

@section('title', 'Каталог книг')

@section('content')
    <section class="hero">
        <div>
            <h1>Каталог книг с онлайн-бронированием</h1>
            <p>Ищите книги по названию, автору или жанру, бронируйте доступные экземпляры и продлевайте срок в личном кабинете.</p>
        </div>
        <div class="hero-panel">
            <strong>Электронная библиотека</strong>
            <p style="margin-top: 8px;">Бронирование создается на 14 дней. Продление доступно онлайн на странице ваших бронирований.</p>
        </div>
    </section>

    <form class="toolbar" method="GET" action="{{ route('catalog.index') }}">
        <div class="field">
            <label for="q">Поиск</label>
            <input id="q" name="q" value="{{ $search }}" placeholder="Название, автор, жанр или ISBN">
        </div>
        <div class="field">
            <label for="genre_id">Жанр</label>
            <select id="genre_id" name="genre_id">
                <option value="">Все жанры</option>
                @foreach($genres as $genre)
                    <option value="{{ $genre->id }}" @selected((string) $selectedGenre === (string) $genre->id)>
                        {{ $genre->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-secondary" type="submit">Найти</button>
        <a class="btn btn-outline" href="{{ route('catalog.index') }}">Сбросить</a>
    </form>

    @if($books->isEmpty())
        <div class="form-card">
            <h2>Книги не найдены</h2>
            <p class="muted">Попробуйте изменить поисковый запрос или выбрать другой жанр.</p>
        </div>
    @else
        <div class="grid book-grid">
            @foreach($books as $book)
                <article class="card">
                    <a class="book-cover" href="{{ route('catalog.show', $book) }}">
                        @if($book->image)
                            <img src="{{ $book->image }}" alt="{{ $book->title }}">
                        @else
                            <span>{{ $book->title }}</span>
                        @endif
                    </a>
                    <div class="card-body">
                        <h2 class="book-title">{{ $book->title }}</h2>
                        <p class="meta">Автор: {{ $book->author->name }}</p>
                        <p class="meta">Жанр: {{ $book->genre->name }}</p>
                        <p class="meta">Год: {{ $book->year ?: 'не указан' }}</p>
                        <div class="actions">
                            @if($book->available_copies > 0)
                                <span class="badge green">Доступно: {{ $book->available_copies }}</span>
                            @else
                                <span class="badge red">Нет экземпляров</span>
                            @endif
                            <a class="btn btn-secondary" href="{{ route('catalog.show', $book) }}">Подробнее</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{ $books->links() }}
    @endif
@endsection
