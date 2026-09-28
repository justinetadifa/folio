@extends('layouts.app')
@section('title', $borrower->name)
@section('section', 'Borrowers')
@section('content')
    <a href="{{ route('borrowers.index') }}" class="text-link text-xs mb-6">
        <x-icon name="back" />Back to borrowers</a>
    <div class="panel p-6 sm:p-8 mb-7">
        <div class="flex flex-wrap items-start gap-5"><span class="avatar !w-16 !h-16 text-2xl"
                aria-hidden="true">{{ mb_substr($borrower->name, 0, 1) }}</span>
            <div class="flex-1 min-w-0">
                <p class="eyebrow">Borrower #{{ str_pad($borrower->borrower_id, 3, '0', STR_PAD_LEFT) }}</p>
                <h1 class="page-title break-words">{{ $borrower->name }}</h1>
                <p class="text-muted mt-3 flex items-center gap-2"><x-icon name="phone" />{{ $borrower->contact_number }}
                </p>
            </div><a href="{{ route('borrowers.edit', $borrower) }}" class="btn btn-secondary"><x-icon name="edit" />Edit
                details</a>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-6 border-t border-line mt-7 pt-6">
            <div class="flex gap-9">
                <div>
                    <p class="detail-label">Active rentals</p>
                    <p class="font-serif text-2xl">{{ $activeCount }}</p>
                </div>
                <div>
                    <p class="detail-label">Total rentals</p>
                    <p class="font-serif text-2xl">{{ $rentals->total() }}</p>
                </div>
            </div>
            <div class="flex gap-3"><a href="{{ route('rentals.create', ['borrower_id' => $borrower->borrower_id]) }}"
                    class="btn btn-primary"><x-icon name="plus" />New rental</a>
                @if ($rentals->total() === 0)
                    <form method="POST" action="{{ route('borrowers.destroy', $borrower) }}"
                        data-confirm="Delete this borrower? This action cannot be undone."
                        data-confirm-label="Delete borrower">@csrf @method('DELETE')<button
                            class="btn btn-danger">Delete</button></form>
                @endif
            </div>
        </div>
    </div>
    <section class="panel">
        <div class="panel-head">
            <h2 class="panel-title">Rental history</h2><span class="text-muted text-xs">{{ $rentals->total() }}
                {{ Str::plural('transaction', $rentals->total()) }}</span>
        </div>@include('rentals._history', ['mode' => 'borrower'])
    </section>{{ $rentals->links() }}@if ($rentals->total() > 0)
        <p class="field-hint mt-4">This borrower’s record is preserved because it has rental history.</p>
    @endif
@endsection
