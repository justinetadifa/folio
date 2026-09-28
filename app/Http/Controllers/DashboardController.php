<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrower;
use App\Models\Rental;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'totalBooks' => Book::count(),
            'availableBooks' => Book::where('status', Book::AVAILABLE)->count(),
            'activeRentals' => Rental::where('status', Rental::RENTED)->count(),
            'borrowerCount' => Borrower::count(),
            'recentRentals' => Rental::with(['book', 'borrower'])->orderByDesc('rental_id')->limit(5)->get(),
            'featuredBooks' => Book::where('status', Book::AVAILABLE)->orderByDesc('book_id')->limit(3)->get(),
        ]);
    }
}
