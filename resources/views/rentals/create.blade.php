@extends('layouts.app')
@section('title', 'New rental')
@section('section', 'Rentals')
@section('content')
    <div class="page-head">
        <div><a href="{{ route('rentals.index') }}" class="text-link text-xs mb-4"><x-icon name="back" />Back to rentals</a>
            <h1 class="page-title">A new chapter begins.</h1>
            <p class="page-subtitle">Match an available book with its next reader.</p>
        </div>
    </div>
    @if ($books->isEmpty() || $borrowers->isEmpty())
        <div class="alert alert-error" role="status"><x-icon name="info" />
            <div>
                <p class="font-semibold">A rental needs an available book and a registered borrower.</p>
                <div class="flex gap-4 mt-2">
                    @if ($books->isEmpty())
                        <a href="{{ route('books.create') }}" class="underline">Add a book</a>
                        @endif @if ($borrowers->isEmpty())
                            <a href="{{ route('borrowers.create') }}" class="underline">Add a borrower</a>
                        @endif
                </div>
            </div>
        </div>
    @endif
    <div class="grid lg:grid-cols-[minmax(0,680px)_1fr] gap-8">
        <form method="POST" action="{{ route('rentals.store') }}" class="form-card">@csrf<div class="form-stack">
                <div><label for="borrower_id" class="field-label">Borrower <span class="text-muted">*</span></label><select
                        id="borrower_id" name="borrower_id" class="input" required
                        @error('borrower_id') aria-invalid="true" aria-describedby="borrower_id-error" @enderror>
                        <option value="">Select a borrower</option>
                        @foreach ($borrowers as $borrower)
                            <option value="{{ $borrower->borrower_id }}" @selected((string) old('borrower_id', $selected['borrower_id'] ?? '') === (string) $borrower->borrower_id)>{{ $borrower->name }}
                                · #{{ $borrower->borrower_id }}</option>
                        @endforeach
                    </select>
                    @error('borrower_id')
                        <p id="borrower_id-error" class="field-error">{{ $message }}</p>
                    @enderror
                    <p class="field-hint">
                        New reader? <a href="{{ route('borrowers.create') }}" class="text-link">Register a borrower</a></p>
                </div>
                <div><label for="book_id" class="field-label">Available book <span
                            class="text-muted">*</span></label><select id="book_id" name="book_id" class="input" required
                        @error('book_id') aria-invalid="true" aria-describedby="book_id-error" @enderror>
                        <option value="">Select a book</option>
                        @foreach ($books as $book)
                            <option value="{{ $book->book_id }}" @selected((string) old('book_id', $selected['book_id'] ?? '') === (string) $book->book_id)>{{ $book->title }} ·
                                {{ $book->author }} · #{{ $book->book_id }}</option>
                        @endforeach
                    </select>
                    @error('book_id')
                        <p id="book_id-error" class="field-error">{{ $message }}</p>
                    @enderror
                    <p class="field-hint">
                        Only books currently marked as Available can be rented.</p>
                </div>
                <div><label for="rental_date" class="field-label">Rental date <span
                            class="text-muted">*</span></label><input type="date" id="rental_date" name="rental_date"
                        class="input" required max="{{ today()->toDateString() }}"
                        value="{{ old('rental_date', today()->toDateString()) }}"
                        @error('rental_date') aria-invalid="true" aria-describedby="rental_date-error" @enderror>
                    @error('rental_date')
                        <p id="rental_date-error" class="field-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="form-actions"><a href="{{ route('rentals.index') }}" class="btn btn-secondary">Cancel</a><button
                    class="btn btn-primary" @disabled($books->isEmpty() || $borrowers->isEmpty())><x-icon name="rental" />Create rental</button>
            </div>
        </form>
        <aside class="form-aside pt-3 max-w-xs">
            <h2>From shelf to reader</h2>
            <div class="step"><span class="step-number">1</span>
                <p>Choose the borrower and an available book.</p>
            </div>
            <div class="step"><span class="step-number">2</span>
                <p>Confirm the rental date. The book will be marked as Rented.</p>
            </div>
            <div class="step"><span class="step-number">3</span>
                <p>When it comes back, open the rental record and record the return.</p>
            </div>
            <p class="text-xs mt-7">Fields marked * are required.</p>
        </aside>
    </div>
@endsection
