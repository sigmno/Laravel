<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');

        $reservations = Reservation::query()
            ->with(['user', 'book.author', 'book.genre'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reservations.index', [
            'reservations' => $reservations,
            'statuses' => Reservation::statuses(),
            'selectedStatus' => $status,
        ]);
    }

    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Reservation::statuses()))],
            'comment' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($reservation, $validated) {
            $reservation->load('book');

            $wasActive = $reservation->isActiveReservation();
            $willBeActive = in_array($validated['status'], Reservation::ACTIVE_STATUSES, true);

            if (! $wasActive && $willBeActive) {
                $this->decreaseAvailableCopies($reservation->book);
            }

            if ($wasActive && ! $willBeActive) {
                $this->increaseAvailableCopies($reservation->book);
            }

            $reservation->update([
                'status' => $validated['status'],
                'comment' => $validated['comment'] ?? null,
            ]);
        });

        return back()->with('success', 'Статус бронирования обновлен.');
    }

    private function decreaseAvailableCopies(Book $book): void
    {
        if ($book->available_copies < 1) {
            throw ValidationException::withMessages([
                'status' => 'Нет доступных экземпляров для перевода бронирования в активный статус.',
            ]);
        }

        $book->decrement('available_copies');
    }

    private function increaseAvailableCopies(Book $book): void
    {
        if ($book->available_copies < $book->total_copies) {
            $book->increment('available_copies');
        }
    }
}
