@extends('layouts.app')

@section('title', 'Книги')

@section('content')
    <section class="page-title">
        <h1>Книги</h1>
        <p>Управление каталогом и количеством доступных экземпляров.</p>
    </section>

    @include('partials.admin-menu')

    <form class="toolbar" method="GET" action="{{ route('admin.books.index') }}">
        <div class="field">
            <label for="q">Поиск</label>
            <input id="q" name="q" value="{{ $search }}" placeholder="Название, автор, жанр или ISBN">
        </div>
        <button class="btn btn-secondary" type="submit">Найти</button>
        <a class="btn btn-outline" href="{{ route('admin.books.index') }}">Сбросить</a>
        <a class="btn btn-primary" href="{{ route('admin.books.create') }}">Добавить книгу</a>
    </form>

    <section class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Книга</th>
                    <th>Автор / жанр</th>
                    <th>ISBN</th>
                    <th>Экземпляры</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($books as $book)
                    <tr>
                        <td>
                            <strong>{{ $book->title }}</strong>
                            <div class="meta">Год: {{ $book->year ?: '—' }}</div>
                        </td>
                        <td>
                            {{ $book->author->name }}<br>
                            <span class="meta">{{ $book->genre->name }}</span>
                        </td>
                        <td>{{ $book->isbn ?: '—' }}</td>
                        <td>{{ $book->available_copies }} из {{ $book->total_copies }}</td>
                        <td>
                            @if($book->is_active)
                                <span class="badge green">активна</span>
                            @else
                                <span class="badge red">скрыта</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions" style="margin-top: 0;">
                                <a class="btn btn-secondary" href="{{ route('admin.books.edit', $book) }}">Изменить</a>
                                <form method="POST" action="{{ route('admin.books.destroy', $book) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Книги еще не добавлены.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $books->links() }}
    </section>
@endsection
