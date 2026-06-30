<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function index(): View
    {
        return view('admin.authors.index', [
            'authors' => Author::withCount('books')->orderBy('name')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.authors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Author::create($this->validatedData($request));

        return redirect()->route('admin.authors.index')->with('success', 'Автор добавлен.');
    }

    public function edit(Author $author): View
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author): RedirectResponse
    {
        $author->update($this->validatedData($request));

        return redirect()->route('admin.authors.index')->with('success', 'Автор обновлен.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        if ($author->books()->exists()) {
            return back()->with('error', 'Нельзя удалить автора, пока к нему привязаны книги.');
        }

        $author->delete();

        return back()->with('success', 'Автор удален.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
        ]);
    }
}
