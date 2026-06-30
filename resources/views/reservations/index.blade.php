@extends('layouts.app')

@section('title', 'Мои бронирования')

@section('content')
    <section class="page-title">
        <h1>Мои бронирования</h1>
        <p>Здесь можно посмотреть сроки, продлить активное бронирование или отменить новое бронирование.</p>
    </section>

    <div class="table-card">
        @if($reservations->isEmpty())
            <p class="muted">У вас пока нет бронирований.</p>
            <a class="btn btn-secondary" href="{{ route('catalog.index') }}">Выбрать книгу</a>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Книга</th>
                        <th>Срок</th>
                        <th>Статус</th>
                        <th>Комментарий</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reservations as $reservation)
                        <tr>
                            <td>
                                <strong>{{ $reservation->book->title }}</strong>
                                <div class="meta">{{ $reservation->book->author->name }} • {{ $reservation->book->genre->name }}</div>
                            </td>
                            <td>
                                {{ $reservation->start_date->format('d.m.Y') }}<br>
                                до {{ $reservation->due_date->format('d.m.Y') }}
                            </td>
                            <td><span class="badge orange">{{ $reservation->statusLabel() }}</span></td>
                            <td>{{ $reservation->comment ?: '—' }}</td>
                            <td>
                                <div class="actions" style="margin-top: 0;">
                                    @if($reservation->canBeExtended())
                                        <form method="POST" action="{{ route('reservations.extend', $reservation) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-secondary" type="submit">Продлить</button>
                                        </form>
                                    @endif
                                    @if($reservation->canBeCancelledByUser())
                                        <form method="POST" action="{{ route('reservations.cancel', $reservation) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-danger" type="submit">Отменить</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{ $reservations->links() }}
        @endif
    </div>
@endsection
