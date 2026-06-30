@extends('layouts.app')

@section('title', 'Личный кабинет')

@section('content')
    <section class="page-title">
        <h1>Личный кабинет</h1>
        <p>Здесь собраны ваши активные бронирования и быстрые действия.</p>
    </section>

    <div class="grid stats-grid">
        <div class="card stat-card">
            <div class="stat-number">{{ $activeReservations }}</div>
            <div class="meta">активных бронирований</div>
        </div>
        <div class="card stat-card">
            <div class="stat-number">{{ $availableBooks }}</div>
            <div class="meta">книг доступно сейчас</div>
        </div>
        <div class="card stat-card">
            <div class="stat-number">{{ auth()->user()->isAdmin() ? 'admin' : 'user' }}</div>
            <div class="meta">роль пользователя</div>
        </div>
        <div class="card stat-card">
            <div class="stat-number">{{ now()->format('d.m') }}</div>
            <div class="meta">сегодня</div>
        </div>
    </div>

    <div class="actions">
        <a class="btn btn-secondary" href="{{ route('catalog.index') }}">Перейти в каталог</a>
        <a class="btn btn-outline" href="{{ route('reservations.index') }}">Мои бронирования</a>
        @if(auth()->user()->isAdmin())
            <a class="btn btn-primary" href="{{ route('admin.dashboard') }}">Открыть админ-панель</a>
        @endif
    </div>

    <section class="table-card" style="margin-top: 24px;">
        <h2>Последние бронирования</h2>
        @if($latestReservations->isEmpty())
            <p class="muted">У вас пока нет бронирований.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Книга</th>
                        <th>Срок</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($latestReservations as $reservation)
                        <tr>
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
