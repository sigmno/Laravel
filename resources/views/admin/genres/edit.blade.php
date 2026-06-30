@extends('layouts.app')

@section('title', 'Редактирование жанра')

@section('content')
    <section class="page-title">
        <h1>Редактирование жанра</h1>
        <p>{{ $genre->name }}</p>
    </section>

    @include('partials.admin-menu')

    <section class="form-card">
        @include('admin.genres.form', [
            'action' => route('admin.genres.update', $genre),
            'method' => 'PATCH',
            'genre' => $genre,
        ])
    </section>
@endsection
