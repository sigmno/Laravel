@extends('layouts.app')

@section('title', 'Новый жанр')

@section('content')
    <section class="page-title">
        <h1>Новый жанр</h1>
        <p>Добавьте название и описание жанра.</p>
    </section>

    @include('partials.admin-menu')

    <section class="form-card">
        @include('admin.genres.form', [
            'action' => route('admin.genres.store'),
            'method' => 'POST',
            'genre' => null,
        ])
    </section>
@endsection
