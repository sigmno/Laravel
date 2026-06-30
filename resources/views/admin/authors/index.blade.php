@extends('layouts.app')

@section('title', 'Авторы')

@section('content')
    <section class="page-title">
        <h1>Авторы</h1>
        <p>Справочник авторов для карточек книг.</p>
    </section>

    @include('partials.admin-menu')

    <div class="actions">
        <a class="btn btn-primary" href="{{ route('admin.authors.create') }}">Добавить автора</a>
    </div>

    <section class="table-card" style="margin-top: 18px;">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Биография</th>
                    <th>Книг</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($authors as $author)
                    <tr>
                        <td><strong>{{ $author->name }}</strong></td>
                        <td>{{ $author->biography ?: '—' }}</td>
                        <td>{{ $author->books_count }}</td>
                        <td>
                            <div class="actions" style="margin-top: 0;">
                                <a class="btn btn-secondary" href="{{ route('admin.authors.edit', $author) }}">Изменить</a>
                                <form method="POST" action="{{ route('admin.authors.destroy', $author) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Авторы еще не добавлены.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $authors->links() }}
    </section>
@endsection
