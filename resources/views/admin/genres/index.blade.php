@extends('layouts.app')

@section('title', 'Жанры')

@section('content')
    <section class="page-title">
        <h1>Жанры</h1>
        <p>Справочник жанров для поиска и карточек книг.</p>
    </section>

    @include('partials.admin-menu')

    <div class="actions">
        <a class="btn btn-primary" href="{{ route('admin.genres.create') }}">Добавить жанр</a>
    </div>

    <section class="table-card" style="margin-top: 18px;">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Книг</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($genres as $genre)
                    <tr>
                        <td><strong>{{ $genre->name }}</strong></td>
                        <td>{{ $genre->description ?: '—' }}</td>
                        <td>{{ $genre->books_count }}</td>
                        <td>
                            <div class="actions" style="margin-top: 0;">
                                <a class="btn btn-secondary" href="{{ route('admin.genres.edit', $genre) }}">Изменить</a>
                                <form method="POST" action="{{ route('admin.genres.destroy', $genre) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Жанры еще не добавлены.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $genres->links() }}
    </section>
@endsection
