<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'activeReservations' => $user->reservations()
                ->whereIn('status', Reservation::ACTIVE_STATUSES)
                ->count(),
            'availableBooks' => Book::where('is_active', true)
                ->where('available_copies', '>', 0)
                ->count(),
            'latestReservations' => $user->reservations()
                ->with(['book.author', 'book.genre'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
