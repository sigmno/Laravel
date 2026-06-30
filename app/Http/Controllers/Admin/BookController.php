<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));

        $books = Book::query()
            ->with(['author', 'genre'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%")
                        ->orWhereHas('author', fn ($author) => $author->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('genre', fn ($genre) => $genre->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.books.index', compact('books', 'search'));
    }

    public function create(): View
    {
        return view('admin.books.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        Book::create($this->validatedData($request));

        return redirect()->route('admin.books.index')->with('success', 'Книга добавлена.');
    }

    public function edit(Book $book): View
    {
        return view('admin.books.edit', [
            'book' => $book,
            ...$this->formData(),
        ]);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $book->update($this->validatedData($request, $book));

        return redirect()->route('admin.books.index')->with('success', 'Книга обновлена.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->reservations()->exists()) {
            return back()->with('error', 'Нельзя удалить книгу, у которой есть бронирования. Отключите ее в карточке книги.');
        }

        $book->delete();

        return back()->with('success', 'Книга удалена.');
    }

    private function formData(): array
    {
        return [
            'authors' => Author::orderBy('name')->get(),
            'genres' => Genre::orderBy('name')->get(),
        ];
    }

    private function validatedData(Request $request, ?Book $book = null): array
    {
        $validated = $request->validate([
            'author_id' => ['required', 'exists:authors,id'],
            'genre_id' => ['required', 'exists:genres,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'year' => ['nullable', 'integer', 'min:0', 'max:2100'],
            'isbn' => [
                'nullable',
                'string',
                'max:32',
                Rule::unique('books', 'isbn')->ignore($book?->id),
            ],
            'total_copies' => ['required', 'integer', 'min:0'],
            'available_copies' => ['required', 'integer', 'min:0', 'lte:total_copies'],
            'image' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['image'] = $validated['image'] ?: null;

        return $validated;
    }
}
