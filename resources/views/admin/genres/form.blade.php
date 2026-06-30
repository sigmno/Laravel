<form method="POST" action="{{ $action }}" class="grid">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="field">
        <label for="name">Название жанра</label>
        <input id="name" name="name" value="{{ old('name', $genre->name ?? '') }}" required>
        @error('name') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="description">Описание</label>
        <textarea id="description" name="description">{{ old('description', $genre->description ?? '') }}</textarea>
        @error('description') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="actions">
        <button class="btn btn-primary" type="submit">Сохранить</button>
        <a class="btn btn-outline" href="{{ route('admin.genres.index') }}">Назад</a>
    </div>
</form>
