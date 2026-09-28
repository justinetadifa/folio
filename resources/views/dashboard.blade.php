@extends('layouts.app')
@section('title', 'Overview')
@section('content')
    <div class="page-head">
        <div>
            <p class="eyebrow">Your library at a glance</p>
            <h1 class="page-title">Your library, in good order.</h1>
            <p class="page-subtitle">Keep the shelves organized and the stories moving.</p>
        </div><a href="{{ route('rentals.create') }}" class="btn btn-primary"><x-icon name="plus" />New rental</a>
    </div>
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach ([[$totalBooks, 'Books in collection', 'book'], [$availableBooks, 'Available to rent', 'open-book'], [$activeRentals, 'Currently rented', 'rental'], [$borrowerCount, 'Registered borrowers', 'users']] as [$value, $label, $icon])
            <div class="stat">
                <div class="flex justify-between items-center"><span
                        class="text-xs font-medium text-muted">{{ $label }}</span><span class="stat-icon"><x-icon
                            :name="$icon" /></span></div>
                <p class="stat-number">{{ str_pad($value, 2, '0', STR_PAD_LEFT) }}</p>
            </div>
        @endforeach
    </div>
    <div class="banner mb-7">
        <div class="banner-copy">
            <p class="uppercase tracking-widest text-[10px] text-[#cfddbe] mb-3">One book. Many beginnings.</p>
            <h2 class="font-serif text-2xl sm:text-[27px] leading-tight">The next great read is waiting.</h2>
            <p class="text-[#d5e0d3] text-xs mt-3 mb-5">Find an available title and connect it with its next reader.</p><a
                href="{{ route('books.index', ['status' => 'Available']) }}" class="text-link !text-white text-xs">Explore the
                collection <x-icon name="arrow" /></a>
        </div><svg class="banner-art" viewBox="0 0 260 210" fill="none" aria-hidden="true">
            <ellipse cx="144" cy="184" rx="96" ry="12" fill="#18382e" opacity=".45" />
            <circle cx="153" cy="99" r="85" stroke="#99ad7f" opacity=".17" />
            <circle cx="153" cy="99" r="69" stroke="#99ad7f" opacity=".14" />
            <g transform="rotate(-10 100 120)">
                <rect x="43" y="52" width="79" height="132" rx="5" fill="#d3ddb4" />
                <path d="M52 53v130" stroke="#a7b688" />
                <rect x="62" y="68" width="43" height="72" rx="22" stroke="#7d9466" />
                <path d="M84 80v48m-12-32 12 10 12-10m-24 16 12 10 12-10" stroke="#7d9466" />
                <path d="M67 155h36m-31 6h26" stroke="#7d9466" />
            </g>
            <g transform="rotate(8 161 115)">
                <rect x="126" y="39" width="78" height="145" rx="5" fill="#e8cd9d" />
                <path d="M135 40v142" stroke="#c2a173" />
                <rect x="145" y="59" width="44" height="100" stroke="#b59161" />
                <circle cx="167" cy="99" r="14" stroke="#b59161" />
                <path d="M153 134h28m-23 7h18" stroke="#b59161" />
            </g>
        </svg>
    </div>
    <div class="grid xl:grid-cols-[1.75fr_1fr] gap-6">
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Recent rentals</h2>
                    <p class="subtext">The latest chapters in your library.</p>
                </div><a href="{{ route('rentals.index') }}" class="text-link text-xs">View all <x-icon
                        name="arrow" /></a>
            </div>
            @if ($recentRentals->isEmpty())<x-empty
                    title="Your first chapter starts here" message="Record a rental to see your library activity."
                    icon="rental"><a href="{{ route('rentals.create') }}" class="btn btn-primary">New rental</a></x-empty>
            @else<div class="table-wrap">
                    <table class="data-table">
                        <caption>Recent rental transactions</caption>
                        <thead>
                            <tr>
                                <th scope="col">Book and borrower</th>
                                <th scope="col">Rented</th>
                                <th scope="col">Status</th>
                                <th scope="col"><span class="sr-only">Open rental</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentRentals as $rental)
                                <tr>
                                    <td><a href="{{ route('rentals.show', $rental) }}"
                                            class="font-semibold hover:underline">{{ $rental->book->title }}</a>
                                        <p class="subtext">{{ $rental->borrower->name }}</p>
                                    </td>
                                    <td class="whitespace-nowrap text-muted">{{ $rental->rental_date->format('M d') }}</td>
                                    <td><x-status :value="$rental->status" /></td>
                                    <td><a href="{{ route('rentals.show', $rental) }}"
                                            aria-label="View rental {{ $rental->rental_id }}" class="text-link p-1"><x-icon
                                                name="arrow" /></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Ready for a new reader</h2>
                    <p class="subtext">Available on the shelf.</p>
                </div>
            </div>
            @forelse($featuredBooks as $book)
                <a href="{{ route('books.show', $book) }}" class="mini-list-item hover:bg-paper"><x-book-mini
                        :book="$book" />
                    <div class="min-w-0 flex-1">
                        <h3 class="font-semibold text-sm">{{ $book->title }}</h3>
                        <p class="subtext">{{ $book->author }}</p>
                    </div><x-icon name="chevron" class="text-muted" />
            </a>@empty<x-empty title="The shelves are quiet"
                    message="Add a book or record a return to make a title available." />
            @endforelse
            <div class="px-6 py-4 border-t border-line"><a href="{{ route('books.index') }}"
                    class="text-link text-xs">Browse all books <x-icon name="arrow" /></a></div>
        </section>
    </div>
@endsection
