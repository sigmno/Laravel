@extends('layouts.app')

@section('title', 'Все бронирования')

@section('content')
    <section class="page-title">
        <h1>Все бронирования</h1>
        <p>Просмотр заявок читателей и изменение статуса бронирования.</p>
    </section>

    @include('partials.admin-menu')

    <form class="toolbar" method="GET" action="{{ route('admin.reservations.index') }}">
        <div class="field">
            <label for="status">Фильтр по статусу</label>
            <select id="status" name="status">
                <option value="">Все статусы</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-secondary" type="submit">Показать</button>
        <a class="btn btn-outline" href="{{ route('admin.reservations.index') }}">Сбросить</a>
    </form>

    <section class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Читатель</th>
                    <th>Книга</th>
                    <th>Срок</th>
                    <th>Текущий статус</th>
                    <th>Изменение статуса</th>
                </tr>
                </thead>
                <tbody>
                @forelse($reservations as $reservation)
                    <tr>
                        <td>
                            <strong>{{ $reservation->user->name }}</strong>
                            <div class="meta">{{ $reservation->user->email }}</div>
                        </td>
                        <td>
                            <strong>{{ $reservation->book->title }}</strong>
                            <div class="meta">{{ $reservation->book->author->name }} • доступно {{ $reservation->book->available_copies }}</div>
                        </td>
                        <td>
                            {{ $reservation->start_date->format('d.m.Y') }}<br>
                            до {{ $reservation->due_date->format('d.m.Y') }}
                        </td>
                        <td>
                            <span class="badge orange">{{ $reservation->statusLabel() }}</span>
                            <div class="meta">{{ $reservation->comment ?: 'без комментария' }}</div>
                        </td>
                        <td>
                            <form class="status-form" method="POST" action="{{ route('admin.reservations.update', $reservation) }}">
                                @csrf
                                @method('PATCH')
                                <select name="status">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" @selected($reservation->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <input name="comment" value="{{ old('comment', $reservation->comment) }}" placeholder="Комментарий">
                                <button class="btn btn-primary" type="submit">Сохранить</button>
                            </form>
                            @error('status') <span class="error-text">{{ $message }}</span> @enderror
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Бронирования не найдены.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $reservations->links() }}
    </section>
@endsection
