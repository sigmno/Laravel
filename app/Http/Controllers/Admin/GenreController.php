<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GenreController extends Controller
{
    public function index(): View
    {
        return view('admin.genres.index', [
            'genres' => Genre::withCount('books')->orderBy('name')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.genres.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Genre::create($this->validatedData($request));

        return redirect()->route('admin.genres.index')->with('success', 'Жанр добавлен.');
    }

    public function edit(Genre $genre): View
    {
        return view('admin.genres.edit', compact('genre'));
    }

    public function update(Request $request, Genre $genre): RedirectResponse
    {
        $genre->update($this->validatedData($request));

        return redirect()->route('admin.genres.index')->with('success', 'Жанр обновлен.');
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        if ($genre->books()->exists()) {
            return back()->with('error', 'Нельзя удалить жанр, пока к нему привязаны книги.');
        }

        $genre->delete();

        return back()->with('success', 'Жанр удален.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
