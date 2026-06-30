<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));
        $genreId = $request->input('genre_id');

        $books = Book::query()
            ->with(['author', 'genre'])
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%")
                        ->orWhereHas('author', fn ($author) => $author->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('genre', fn ($genre) => $genre->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($genreId, fn ($query) => $query->where('genre_id', $genreId))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('catalog.index', [
            'books' => $books,
            'genres' => Genre::orderBy('name')->get(),
            'search' => $search,
            'selectedGenre' => $genreId,
        ]);
    }

    public function show(Book $book): View
    {
        abort_if(! $book->is_active, 404);

        $book->load(['author', 'genre']);

        $hasActiveReservation = auth()->check()
            ? Reservation::where('user_id', auth()->id())
                ->where('book_id', $book->id)
                ->whereIn('status', Reservation::ACTIVE_STATUSES)
                ->exists()
            : false;

        return view('catalog.show', [
            'book' => $book,
            'hasActiveReservation' => $hasActiveReservation,
        ]);
    }
}
