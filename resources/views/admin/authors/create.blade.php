@extends('layouts.app')

@section('title', 'Новый автор')

@section('content')
    <section class="page-title">
        <h1>Новый автор</h1>
        <p>Добавьте имя и краткую биографию автора.</p>
    </section>

    @include('partials.admin-menu')

    <section class="form-card">
        @include('admin.authors.form', [
            'action' => route('admin.authors.store'),
            'method' => 'POST',
            'author' => null,
        ])
    </section>
@endsection
