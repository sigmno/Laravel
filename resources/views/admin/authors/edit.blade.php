@extends('layouts.app')

@section('title', 'Редактирование автора')

@section('content')
    <section class="page-title">
        <h1>Редактирование автора</h1>
        <p>{{ $author->name }}</p>
    </section>

    @include('partials.admin-menu')

    <section class="form-card">
        @include('admin.authors.form', [
            'action' => route('admin.authors.update', $author),
            'method' => 'PATCH',
            'author' => $author,
        ])
    </section>
@endsection
