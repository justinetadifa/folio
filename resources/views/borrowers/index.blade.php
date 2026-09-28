@extends('layouts.app')
@section('title', 'Borrowers')
@section('section', 'Borrowers')
@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">The reading community</p>
            <h1 class="page-title">People behind the pages.</h1>
            <p class="page-subtitle">Keep borrower details and their reading history together.</p>
        </div><a href="{{ route('borrowers.create') }}" class="btn btn-primary"><x-icon name="plus" />Add borrower</a>
    </div>
    <form method="GET" action="{{ route('borrowers.index') }}" class="toolbar" role="search">
        <div class="search-field"><label for="q" class="sr-only">Search borrowers by name or contact</label><x-icon
                name="search" /><input type="search" id="q" name="q" class="input" maxlength="100"
                value="{{ request('q') }}" placeholder="Search by name or contact number…"></div><button
            class="btn btn-secondary">Search</button>
        @if (request()->filled('q'))
            <a href="{{ route('borrowers.index') }}" class="text-link text-xs px-2">Clear search</a>
        @endif
    </form>
    <div class="panel">
        <div class="panel-head">
            <h2 class="panel-title">Borrower directory</h2><span class="text-muted text-xs">{{ $borrowers->total() }}
                {{ Str::plural('borrower', $borrowers->total()) }}</span>
        </div>
        @if ($borrowers->isEmpty())<x-empty title="No borrowers found"
                message="Try a different search or welcome your first reader." icon="users"><a
                href="{{ route('borrowers.create') }}" class="btn btn-primary">Add borrower</a></x-empty>@else<div
                class="table-wrap">
                <table class="data-table">
                    <caption>Registered borrowers</caption>
                    <thead>
                        <tr>
                            <th scope="col">Borrower</th>
                            <th scope="col">Contact number</th>
                            <th scope="col">Active rentals</th>
                            <th scope="col">Total rentals</th>
                            <th scope="col"><span class="sr-only">Details</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($borrowers as $borrower)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3"><span class="avatar"
                                            aria-hidden="true">{{ mb_substr($borrower->name, 0, 1) }}</span>
                                        <div><a href="{{ route('borrowers.show', $borrower) }}"
                                                class="font-semibold hover:underline">{{ $borrower->name }}</a>
                                            <p class="subtext">#{{ str_pad($borrower->borrower_id, 3, '0', STR_PAD_LEFT) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted whitespace-nowrap">{{ $borrower->contact_number }}</td>
                                <td>{{ $borrower->active_rentals_count }}</td>
                                <td>{{ $borrower->rentals_count }}</td>
                                <td><a href="{{ route('borrowers.show', $borrower) }}" class="text-link text-xs">Details
                                        <x-icon name="arrow" /></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>{{ $borrowers->links() }}
@endsection
