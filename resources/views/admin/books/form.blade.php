<form method="POST" action="{{ $action }}" class="form-grid">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="field">
        <label for="title">Название</label>
        <input id="title" name="title" value="{{ old('title', $book->title ?? '') }}" required>
        @error('title') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="isbn">ISBN</label>
        <input id="isbn" name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}">
        @error('isbn') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="author_id">Автор</label>
        <select id="author_id" name="author_id" required>
            <option value="">Выберите автора</option>
            @foreach($authors as $author)
                <option value="{{ $author->id }}" @selected((string) old('author_id', $book->author_id ?? '') === (string) $author->id)>
                    {{ $author->name }}
                </option>
            @endforeach
        </select>
        @error('author_id') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="genre_id">Жанр</label>
        <select id="genre_id" name="genre_id" required>
            <option value="">Выберите жанр</option>
            @foreach($genres as $genre)
                <option value="{{ $genre->id }}" @selected((string) old('genre_id', $book->genre_id ?? '') === (string) $genre->id)>
                    {{ $genre->name }}
                </option>
            @endforeach
        </select>
        @error('genre_id') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="year">Год</label>
        <input id="year" name="year" type="number" min="0" max="2100" value="{{ old('year', $book->year ?? '') }}">
        @error('year') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="image">Ссылка на изображение</label>
        <input id="image" name="image" value="{{ old('image', $book->image ?? '') }}" placeholder="https://...">
        @error('image') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="total_copies">Всего экземпляров</label>
        <input id="total_copies" name="total_copies" type="number" min="0" value="{{ old('total_copies', $book->total_copies ?? 1) }}" required>
        @error('total_copies') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="available_copies">Доступно экземпляров</label>
        <input id="available_copies" name="available_copies" type="number" min="0" value="{{ old('available_copies', $book->available_copies ?? 1) }}" required>
        @error('available_copies') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field full">
        <label for="description">Описание</label>
        <textarea id="description" name="description">{{ old('description', $book->description ?? '') }}</textarea>
        @error('description') <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field full">
        <label class="checkbox-row">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $book->is_active ?? true))>
            <span>Показывать книгу в каталоге</span>
        </label>
    </div>

    <div class="actions field full">
        <button class="btn btn-primary" type="submit">Сохранить</button>
        <a class="btn btn-outline" href="{{ route('admin.books.index') }}">Назад</a>
    </div>
</form>
