@extends('layouts.app')

@section('title', 'Редактирование книги')

@section('content')
    <section class="page-title">
        <h1>Редактирование книги</h1>
        <p>{{ $book->title }}</p>
    </section>

    @include('partials.admin-menu')

    <section class="form-card">
        @include('admin.books.form', [
            'action' => route('admin.books.update', $book),
            'method' => 'PATCH',
            'book' => $book,
        ])
    </section>
@endsection
