<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnRentalRequest;
use App\Http\Requests\StoreRentalRequest;
use App\Models\Book;
use App\Models\Borrower;
use App\Models\Rental;
use App\Services\RentalService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', Rule::in([Rental::RENTED, Rental::RETURNED])]]);
        $rentals = Rental::with(['book', 'borrower'])
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->where(fn ($q) => $q->whereHas('book', fn ($q) => $q->where('title', 'like', "%{$term}%"))->orWhereHas('borrower', fn ($q) => $q->where('name', 'like', "%{$term}%"))))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('rental_id')->paginate(10)->withQueryString();

        return view('rentals.index', compact('rentals'));
    }

    public function create(Request $request)
    {
        $selected = $request->validate(['book_id' => ['nullable', 'integer'], 'borrower_id' => ['nullable', 'integer']]);

        return view('rentals.create', [
            'books' => Book::where('status', Book::AVAILABLE)->orderBy('title')->get(),
            'borrowers' => Borrower::orderBy('name')->get(), 'selected' => $selected,
        ]);
    }

    public function store(StoreRentalRequest $request, RentalService $service)
    {
        $rental = $service->rent($request->validated());

        return redirect()->route('rentals.show', $rental)->with('success', 'Rental recorded. The book is now marked as rented.');
    }

    public function show(Rental $rental)
    {
        $rental->load(['book', 'borrower']);

        return view('rentals.show', compact('rental'));
    }

    public function returnBook(ReturnRentalRequest $request, Rental $rental, RentalService $service)
    {
        $service->returnBook($rental, $request->validated('return_date'));

        return redirect()->route('rentals.show', $rental)->with('success', 'Return recorded. The book is available again.');
    }
}
