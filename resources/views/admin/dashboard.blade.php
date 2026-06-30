@extends('layouts.app')

@section('title', 'Админ-панель')

@section('content')
    <section class="page-title">
        <h1>Админ-панель</h1>
        <p>Управление каталогом, авторами, жанрами и бронированиями.</p>
    </section>

    @include('partials.admin-menu')

    <div class="grid stats-grid">
        <div class="card stat-card">
            <div class="stat-number">{{ $booksCount }}</div>
            <div class="meta">книг</div>
        </div>
        <div class="card stat-card">
            <div class="stat-number">{{ $authorsCount }}</div>
            <div class="meta">авторов</div>
        </div>
        <div class="card stat-card">
            <div class="stat-number">{{ $genresCount }}</div>
            <div class="meta">жанров</div>
        </div>
        <div class="card stat-card">
            <div class="stat-number">{{ $activeReservationsCount }}</div>
            <div class="meta">активных бронирований</div>
        </div>
    </div>

    <section class="table-card" style="margin-top: 24px;">
        <div class="actions" style="justify-content: space-between; margin-top: 0;">
            <h2>Последние бронирования</h2>
            <a class="btn btn-primary" href="{{ route('admin.reservations.index') }}">Все бронирования</a>
        </div>

        @if($latestReservations->isEmpty())
            <p class="muted">Бронирований пока нет.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Читатель</th>
                        <th>Книга</th>
                        <th>Срок</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($latestReservations as $reservation)
                        <tr>
                            <td>{{ $reservation->user->name }}</td>
                            <td>{{ $reservation->book->title }}</td>
                            <td>{{ $reservation->start_date->format('d.m.Y') }} - {{ $reservation->due_date->format('d.m.Y') }}</td>
                            <td><span class="badge orange">{{ $reservation->statusLabel() }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
