<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowerRequest;
use App\Models\Borrower;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowerController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $borrowers = Borrower::withCount(['rentals', 'rentals as active_rentals_count' => fn ($q) => $q->where('status', Rental::RENTED)])
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('contact_number', 'like', "%{$term}%")))
            ->orderBy('name')->paginate(10)->withQueryString();

        return view('borrowers.index', compact('borrowers'));
    }

    public function create()
    {
        return view('borrowers.create');
    }

    public function store(BorrowerRequest $request)
    {
        $borrower = Borrower::create($request->validated());

        return redirect()->route('borrowers.show', $borrower)->with('success', 'Borrower registered.');
    }

    public function show(Borrower $borrower)
    {
        $rentals = $borrower->rentals()->with('book')->orderByDesc('rental_id')->paginate(8);
        $activeCount = $borrower->rentals()->where('status', Rental::RENTED)->count();

        return view('borrowers.show', compact('borrower', 'rentals', 'activeCount'));
    }

    public function edit(Borrower $borrower)
    {
        return view('borrowers.edit', compact('borrower'));
    }

    public function update(BorrowerRequest $request, Borrower $borrower)
    {
        $borrower->update($request->validated());

        return redirect()->route('borrowers.show', $borrower)->with('success', 'Borrower details updated.');
    }

    public function destroy(Borrower $borrower)
    {
        DB::transaction(function () use ($borrower) {
            $current = Borrower::whereKey($borrower->borrower_id)->lockForUpdate()->firstOrFail();
            if ($current->rentals()->exists()) {
                throw ValidationException::withMessages(['delete' => 'This borrower has rental history and cannot be deleted. Their transaction records are preserved.']);
            }
            $current->delete();
        }, 5);

        return redirect()->route('borrowers.index')->with('success', 'Borrower deleted.');
    }
}
