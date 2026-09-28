@extends('layouts.app')
@section('title', 'Rental R-' . str_pad($rental->rental_id, 4, '0', STR_PAD_LEFT))
@section('section', 'Rentals')
@section('content')
    <a href="{{ route('rentals.index') }}" class="text-link text-xs mb-6">
        <x-icon name="back" />Back to rentals</a>
    <div class="page-head">
        <div>
            <p class="eyebrow">Rental record</p>
            <h1 class="page-title">R-{{ str_pad($rental->rental_id, 4, '0', STR_PAD_LEFT) }}</h1>
            <p class="page-subtitle">
                {{ $rental->status === 'Rented' ? 'This book is out with its reader.' : 'A completed chapter, kept on record.' }}
            </p>
        </div><x-status :value="$rental->status" />
    </div>
    <div class="grid lg:grid-cols-[1.35fr_1fr] gap-6">
        <section class="panel p-6 sm:p-8">
            <div class="flex gap-5 items-center pb-6 mb-6 border-b border-line"><x-book-mini :book="$rental->book" />
                <div>
                    <p class="detail-label">Book</p>
                    <h2 class="font-serif text-xl"><a href="{{ route('books.show', $rental->book) }}"
                            class="hover:underline">{{ $rental->book->title }}</a></h2>
                    <p class="subtext">{{ $rental->book->author }}</p>
                </div>
            </div>
            <div class="detail-grid">
                <div>
                    <p class="detail-label">Borrower</p>
                    <p class="detail-value"><a href="{{ route('borrowers.show', $rental->borrower) }}"
                            class="text-link">{{ $rental->borrower->name }}</a></p>
                </div>
                <div>
                    <p class="detail-label">Contact</p>
                    <p class="detail-value break-words">{{ $rental->borrower->contact_number }}</p>
                </div>
                <div>
                    <p class="detail-label">Rental date</p>
                    <p class="detail-value">{{ $rental->rental_date->format('F d, Y') }}</p>
                </div>
                <div>
                    <p class="detail-label">Return date</p>
                    <p class="detail-value">{{ $rental->return_date?->format('F d, Y') ?? 'Not yet returned' }}</p>
                </div>
            </div>
        </section>
        @if ($rental->status === 'Rented')
            <section class="panel p-6 sm:p-8">
                <div class="stat-icon mb-4"><x-icon name="rental" /></div>
                <h2 class="font-serif text-xl">Welcome the book back.</h2>
                <p class="text-muted text-sm leading-relaxed mt-3 mb-6">Record its return to make this title available for
                    the next reader.</p>
                <form method="POST" action="{{ route('rentals.return', $rental) }}"
                    data-confirm="Record this return? The book will become available for another rental."
                    data-confirm-label="Confirm return">@csrf @method('PATCH')<label for="return_date"
                        class="field-label">Return date</label><input type="date" id="return_date" name="return_date"
                        class="input" min="{{ $rental->rental_date->toDateString() }}"
                        max="{{ today()->toDateString() }}" value="{{ old('return_date', today()->toDateString()) }}"
                        required @error('return_date') aria-invalid="true" aria-describedby="return_date-error" @enderror>
                    @error('return_date')
                        <p id="return_date-error" class="field-error">{{ $message }}</p>
                    @enderror
                    <button class="btn btn-primary w-full mt-5">
                        <x-icon name="check" />Record return</button>
                </form>
        </section>@else<section class="panel p-6 sm:p-8">
                <div class="stat-icon mb-4"><x-icon name="check-circle" /></div>
                <h2 class="font-serif text-xl">Back on the record.</h2>
                <p class="text-muted leading-relaxed mt-3">Returned on {{ $rental->return_date?->format('F d, Y') }}. This
                    transaction is complete and stays in the rental history.</p>
                <p class="text-muted text-sm mt-4">Current book status: <x-status :value="$rental->book->status" /></p>
                @if ($rental->book->status === 'Available')
                    <a href="{{ route('rentals.create', ['book_id' => $rental->book_id]) }}"
                        class="btn btn-secondary mt-6">Rent this book again <x-icon name="arrow" /></a>
                @endif
            </section>
        @endif
    </div>
@endsection
