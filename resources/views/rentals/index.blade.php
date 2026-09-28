@extends('layouts.app')
@section('title', 'Rentals')
@section('section', 'Rentals')
@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Books on the move</p>
            <h1 class="page-title">Every loan, accounted for.</h1>
            <p class="page-subtitle">Follow a book from the shelf to its reader and back again.</p>
        </div><a href="{{ route('rentals.create') }}" class="btn btn-primary"><x-icon name="plus" />New rental</a>
    </div>
    <form method="GET" action="{{ route('rentals.index') }}" class="toolbar" role="search">
        <div class="search-field"><label for="q" class="sr-only">Search rentals by book or borrower</label><x-icon
                name="search" /><input type="search" id="q" name="q" class="input" maxlength="100"
                value="{{ request('q') }}" placeholder="Search by book title or borrower…"></div><label class="sr-only"
            for="status">Rental status</label><select id="status" name="status" class="input !w-auto">
            <option value="">All transactions</option value="Rented" @selected(request('status') === 'Rented')>Active rentals
            </option value="Returned" @selected(request('status') === 'Returned')>Returned</option>
        </select><button class="btn btn-secondary">Search</button>
        @if (request()->filled('q') || request()->filled('status'))
            <a href="{{ route('rentals.index') }}" class="text-link text-xs px-2">Clear filters</a>
        @endif
    </form>
    <section class="panel">
        <div class="panel-head">
            <h2 class="panel-title">Rental ledger</h2><span class="text-muted text-xs">{{ $rentals->total() }}
                {{ Str::plural('transaction', $rentals->total()) }}</span>
        </div>
        @if ($rentals->isEmpty())
            <x-empty title="No transactions found" message="Try another search, or start a new rental to get a book moving."
            icon="rental"><a href="{{ route('rentals.create') }}" class="btn btn-primary">New rental</a></x-empty>@else
            <div class="table-wrap">
                <table class="data-table">
                    <caption>Book rental transactions</caption>
                    <thead>
                        <tr>
                            <th scope="col">Book</th>
                            <th scope="col">Borrower</th>
                            <th scope="col">Rented on</th>
                            <th scope="col">Returned on</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rentals as $rental)
                            <tr>
                                <td><a href="{{ route('books.show', $rental->book) }}"
                                        class="font-semibold hover:underline">{{ $rental->book->title }}</a>
                                    <p class="subtext">R-{{ str_pad($rental->rental_id, 4, '0', STR_PAD_LEFT) }}</p>
                                </td>
                                <td><a href="{{ route('borrowers.show', $rental->borrower) }}"
                                        class="hover:underline">{{ $rental->borrower->name }}</a></td>
                                <td class="whitespace-nowrap text-muted">{{ $rental->rental_date->format('M d, Y') }}</td>
                                <td class="whitespace-nowrap text-muted">
                                    {{ $rental->return_date?->format('M d, Y') ?? '—' }}</td>
                                <td><x-status :value="$rental->status" /></td>
                                <td><a href="{{ route('rentals.show', $rental) }}"
                                        class="text-link text-xs whitespace-nowrap">{{ $rental->status === 'Rented' ? 'View / return' : 'View record' }}<x-icon
                                            name="arrow" /></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>{{ $rentals->links() }}
@endsection
