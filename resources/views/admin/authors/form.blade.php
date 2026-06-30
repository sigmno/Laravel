<form method="POST" action="{{ $action }}" class="grid">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="field">
        <label for="name">Имя автора</label>
        <input id="name" name="name" value="{{ old('name', $author->name ?? '') }}" required>
        @error('name') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="biography">Биография</label>
        <textarea id="biography" name="biography">{{ old('biography', $author->biography ?? '') }}</textarea>
        @error('biography') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="actions">
        <button class="btn btn-primary" type="submit">Сохранить</button>
        <a class="btn btn-outline" href="{{ route('admin.authors.index') }}">Назад</a>
    </div>
</form>
