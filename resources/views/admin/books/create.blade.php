@extends('layouts.app')

@section('title', 'Новая книга')

@section('content')
    <section class="page-title">
        <h1>Новая книга</h1>
        <p>Заполните карточку книги и количество экземпляров.</p>
    </section>

    @include('partials.admin-menu')

    <section class="form-card">
        @include('admin.books.form', [
            'action' => route('admin.books.store'),
            'method' => 'POST',
            'book' => null,
        ])
    </section>
@endsection
