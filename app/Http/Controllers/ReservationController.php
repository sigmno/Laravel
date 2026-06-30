<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        return view('reservations.index', [
            'reservations' => $request->user()
                ->reservations()
                ->with(['book.author', 'book.genre'])
                ->latest()
                ->paginate(10),
        ]);
    }

    public function store(Request $request, Book $book): RedirectResponse
    {
        $created = DB::transaction(function () use ($request, $book) {
            $book = Book::whereKey($book->id)->firstOrFail();

            if (! $book->is_available) {
                return false;
            }

            $hasActiveReservation = Reservation::where('user_id', $request->user()->id)
                ->where('book_id', $book->id)
                ->whereIn('status', Reservation::ACTIVE_STATUSES)
                ->exists();

            if ($hasActiveReservation) {
                return null;
            }

            Reservation::create([
                'user_id' => $request->user()->id,
                'book_id' => $book->id,
                'start_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'status' => Reservation::STATUS_NEW,
                'comment' => 'Бронирование создано пользователем онлайн.',
            ]);

            $book->decrement('available_copies');

            return true;
        });

        if ($created === false) {
            return back()->with('error', 'Эта книга сейчас недоступна для бронирования.');
        }

        if ($created === null) {
            return back()->with('error', 'У вас уже есть активное бронирование этой книги.');
        }

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Книга успешно забронирована на 14 дней.');
    }

    public function extend(Request $request, Reservation $reservation): RedirectResponse
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);

        if (! $reservation->canBeExtended()) {
            return back()->with('error', 'Это бронирование нельзя продлить.');
        }

        $baseDate = $reservation->due_date->isFuture()
            ? $reservation->due_date->copy()
            : Carbon::today();

        $reservation->update([
            'due_date' => $baseDate->addDays(7)->toDateString(),
            'status' => Reservation::STATUS_EXTENDED,
            'comment' => 'Срок продлен пользователем онлайн.',
        ]);

        return back()->with('success', 'Срок бронирования продлен на 7 дней.');
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);

        if (! $reservation->canBeCancelledByUser()) {
            return back()->with('error', 'Это бронирование нельзя отменить самостоятельно.');
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status' => Reservation::STATUS_CANCELLED,
                'comment' => 'Бронирование отменено пользователем.',
            ]);

            $this->increaseAvailableCopies($reservation->book);
        });

        return back()->with('success', 'Бронирование отменено.');
    }

    private function increaseAvailableCopies(Book $book): void
    {
        if ($book->available_copies < $book->total_copies) {
            $book->increment('available_copies');
        }
    }
}
