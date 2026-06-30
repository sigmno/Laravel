<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'booksCount' => Book::count(),
            'authorsCount' => Author::count(),
            'genresCount' => Genre::count(),
            'usersCount' => User::count(),
            'newReservationsCount' => Reservation::where('status', Reservation::STATUS_NEW)->count(),
            'activeReservationsCount' => Reservation::whereIn('status', Reservation::ACTIVE_STATUSES)->count(),
            'latestReservations' => Reservation::with(['user', 'book.author'])
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
