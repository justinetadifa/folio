@extends('layouts.app')
@section('title', 'The Bookshelf')
@section('section', 'Books')

@section('content')
    @php
        $isSearching = request()->filled('q') || request()->filled('status');
        $isFirstPage = !request()->has('page') || request('page') == 1;
        $featuredBook = ($isFirstPage && $books->isNotEmpty()) ? $books->first() : null;
        $availableCount = \App\Models\Book::where('status', \App\Models\Book::AVAILABLE)->count();
        $rentedCount = \App\Models\Book::where('status', \App\Models\Book::RENTED)->count();
        $availablePicks = $books->filter(fn($b) => (!$featuredBook || $b->book_id !== $featuredBook->book_id))->take(2);
    @endphp

    <!-- Instant Restore Toast Banner for Mistakenly Deleted Volumes -->
    @if (session('restorable_id'))
        <div class="deleted-restore-toast mb-6" role="status">
            <div class="toast-left-content">
                <div class="toast-icon-circle">
                    <x-icon name="rotate-ccw" class="w-4 h-4 text-amber-800" />
                </div>
                <div>
                    <h4 class="toast-heading">Volume Moved to Recently Deleted</h4>
                    <p class="toast-body">
                        "<strong>{{ session('restorable_title') }}</strong>" was removed from active shelves. Mistakenly deleted? Restore it right away.
                    </p>
                </div>
            </div>
            <div class="toast-actions">
                <form method="POST" action="{{ route('books.restore', session('restorable_id')) }}">
                    @csrf
                    <button type="submit" class="btn-toast-restore">
                        <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                        <span>Undo & Restore Volume</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Compact Integrated Header & Control Bar -->
    <div class="shelf-action-bar mb-6">
        <div class="shelf-title-group">
            <h1 class="shelf-main-title font-serif">The Bookshelf</h1>
            <p class="shelf-subtitle">
                @if ($isSearching)
                    Found {{ $books->total() }} matching {{ Str::plural('volume', $books->total()) }} in catalog
                @else
                    {{ $books->total() }} {{ Str::plural('volume', $books->total()) }} cataloged · {{ $availableCount }} available on shelf
                @endif
            </p>
        </div>

        <div class="shelf-controls-group">
            <!-- Inline Search & Filter Form -->
            <form method="GET" action="{{ route('books.index') }}" class="shelf-search-form" role="search">
                <div class="shelf-input-wrap">
                    <label for="q" class="sr-only">Search books by title or author</label>
                    <x-icon name="search" class="shelf-search-icon" />
                    <input id="q" name="q" type="search" value="{{ request('q') }}" maxlength="100"
                        class="shelf-search-input" placeholder="Search title or author…">
                </div>

                <div class="shelf-filter-wrap">
                    <label for="status" class="sr-only">Filter availability</label>
                    <select id="status" name="status" class="shelf-select">
                        <option value="">All status</option>
                        <option value="Available" @selected(request('status') === 'Available')>Available only</option>
                        <option value="Rented" @selected(request('status') === 'Rented')>On loan</option>
                    </select>
                </div>

                <button type="submit" class="shelf-submit-btn" title="Search catalog">
                    <span>Search</span>
                </button>

                @if ($isSearching)
                    <a href="{{ route('books.index') }}" class="shelf-clear-btn" title="Reset filters">
                        <x-icon name="close" class="w-3.5 h-3.5" />
                        <span>Clear</span>
                    </a>
                @endif
            </form>

            <!-- Recently Deleted Archive Button -->
            <button type="button" id="toggle-trash-btn" class="btn-shelf-trash" aria-expanded="false" title="View recently deleted volumes">
                <x-icon name="history" class="w-3.5 h-3.5 text-amber-700" />
                <span class="hidden sm:inline">Recently Deleted</span>
                @if (isset($deletedBooks) && $deletedBooks->isNotEmpty())
                    <span class="trash-badge-count">{{ $deletedBooks->count() }}</span>
                @endif
            </button>

            <a href="{{ route('books.create') }}" class="btn-shelf-add">
                <x-icon name="plus" class="w-4 h-4 text-brass-light" />
                <span>Add book</span>
            </a>
        </div>
    </div>

    <!-- Expandable Recently Deleted Volumes Drawer -->
    <div id="trash-drawer" class="trash-archive-drawer mb-8" style="display: none;">
        <div class="trash-drawer-header">
            <div class="flex items-center gap-3">
                <div class="trash-header-icon-box">
                    <x-icon name="history" class="w-4 h-4 text-amber-800" />
                </div>
                <div>
                    <h3 class="trash-header-title font-serif">Recently Deleted Volumes Archive</h3>
                    <p class="trash-header-subtitle">Restore mistakenly deleted books with 1 click, or permanently purge them from the catalog.</p>
                </div>
            </div>
            <button type="button" id="close-trash-btn" class="trash-close-action" aria-label="Close archive drawer">
                <x-icon name="close" class="w-4 h-4" />
            </button>
        </div>

        @if (!isset($deletedBooks) || $deletedBooks->isEmpty())
            <div class="trash-empty-notice">
                <p class="text-sm font-medium text-ink">Archive holding vault is empty</p>
                <p class="text-xs text-muted mt-0.5">No deleted volumes recorded. All titles remain safely cataloged on active shelves.</p>
            </div>
        @else
            <div class="trash-grid">
                @foreach ($deletedBooks as $deleted)
                    <div class="trash-card">
                        <div class="trash-card-info">
                            <span class="trash-card-id font-serif">#{{ str_pad($deleted->original_book_id ?? $deleted->id, 3, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h4 class="trash-card-title font-serif">{{ $deleted->title }}</h4>
                                <p class="trash-card-author">By {{ $deleted->author }} · {{ $deleted->published_year }} · ₱{{ number_format((float) $deleted->price, 2) }}</p>
                                <span class="trash-card-time">Deleted {{ $deleted->deleted_at ? $deleted->deleted_at->diffForHumans() : 'recently' }}</span>
                            </div>
                        </div>
                        <div class="trash-card-actions">
                            <form method="POST" action="{{ route('books.restore', $deleted->id) }}">
                                @csrf
                                <button type="submit" class="btn-trash-restore" title="Restore this book to the active catalog">
                                    <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                                    <span>Restore to shelf</span>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('books.purge', $deleted->id) }}"
                                data-confirm="Permanently purge '{{ $deleted->title }}' from database? This cannot be undone."
                                data-confirm-label="Purge volume">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-trash-purge" title="Permanently delete from database">
                                    <x-icon name="delete" class="w-3.5 h-3.5" />
                                    <span>Purge</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Active Search Filter Strip -->
    @if ($isSearching)
        <div class="active-filter-strip">
            <div class="active-filter-tags">
                <span class="filter-lead">Active filters:</span>
                @if (request('q'))
                    <span class="filter-pill">
                        <span>Keyword: <strong>{{ request('q') }}</strong></span>
                    </span>
                @endif
                @if (request('status'))
                    <span class="filter-pill">
                        <span>Status: <strong>{{ request('status') === 'Available' ? 'Available only' : 'On loan' }}</strong></span>
                    </span>
                @endif
                <span class="filter-count">({{ $books->total() }} {{ Str::plural('matching volume', $books->total()) }})</span>
            </div>
            <a href="{{ route('books.index') }}" class="clear-all-link">
                <x-icon name="close" class="w-3 h-3" />
                <span>Reset all filters</span>
            </a>
        </div>
    @endif

    <!-- Editorial Discovery Highlight (Featured Book + Quick Shelf Picks) -->
    @if ($isFirstPage && !$isSearching && $featuredBook)
        <section class="discovery-section mb-9" aria-label="Featured Collection Highlights">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
                <!-- 1. Featured Spotlight Volume (7-8 columns) -->
                <div class="lg:col-span-7 xl:col-span-8 featured-panel">
                    <div class="featured-badge">
                        <x-icon name="sparkles" class="w-3.5 h-3.5 text-brass" />
                        <span>Featured Volume</span>
                    </div>

                    <div class="featured-grid">
                        <div class="featured-cover-column">
                            <a href="{{ route('books.show', $featuredBook) }}" class="featured-cover-link">
                                <x-book-cover :book="$featuredBook" size="large" />
                            </a>
                        </div>

                        <div class="featured-details-column">
                            <div class="flex items-center gap-2 mb-2">
                                <x-status :value="$featuredBook->status" />
                                <span class="record-id">#{{ str_pad($featuredBook->book_id, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            <h2 class="featured-book-title font-serif">
                                <a href="{{ route('books.show', $featuredBook) }}">
                                    {{ $featuredBook->title }}
                                </a>
                            </h2>

                            <p class="featured-book-author">By <span class="font-medium text-ink">{{ $featuredBook->author }}</span> · First published {{ $featuredBook->published_year }}</p>

                            @if ($featuredBook->description)
                                <p class="featured-book-desc">
                                    {{ Str::limit($featuredBook->description, 150) }}
                                </p>
                            @endif

                            <div class="featured-bottom-row">
                                <div class="featured-valuation">
                                    <span class="valuation-label">Valuation</span>
                                    <span class="valuation-price">₱{{ number_format((float) $featuredBook->price, 2) }}</span>
                                </div>

                                <a href="{{ route('books.show', $featuredBook) }}" class="btn btn-featured-action">
                                    <span>View volume</span>
                                    <x-icon name="arrow" class="w-3.5 h-3.5" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Shelf Companions / Quick Picks (4-5 columns) -->
                <div class="lg:col-span-5 xl:col-span-4 quick-picks-panel">
                    <div class="picks-header">
                        <div>
                            <span class="picks-eyebrow">Shelf Highlights</span>
                            <h3 class="picks-title font-serif">
                                {{ request('status') === 'Rented' ? 'On Loan' : 'Ready to Borrow' }}
                            </h3>
                        </div>
                        <a href="{{ route('books.index', ['status' => 'Available']) }}" class="picks-browse-link">
                            <span>Available ({{ $availableCount }})</span>
                            <x-icon name="chevron" class="w-3 h-3" />
                        </a>
                    </div>

                    <div class="picks-list">
                        @forelse ($availablePicks as $pick)
                            <a href="{{ route('books.show', $pick) }}" class="pick-item-card">
                                <div class="pick-cover-wrap">
                                    <x-book-cover :book="$pick" size="compact" />
                                </div>
                                <div class="pick-meta">
                                    <h4 class="pick-book-title font-serif">{{ $pick->title }}</h4>
                                    <p class="pick-book-author">{{ $pick->author }}</p>
                                    <div class="pick-footer-meta">
                                        <span class="text-xs text-muted">₱{{ number_format((float) $pick->price, 2) }}</span>
                                        <x-status :value="$pick->status" />
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-xs text-muted py-6 text-center">No companion titles found.</p>
                        @endforelse
                    </div>

                    <div class="picks-bottom-action">
                        <a href="{{ route('rentals.create') }}" class="btn-new-rental-shortcut">
                            <x-icon name="rental" class="w-4 h-4 text-brass" />
                            <span>Start a rental</span>
                            <x-icon name="arrow" class="w-3.5 h-3.5 ml-auto opacity-75" />
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- All Books Grid Section -->
    <section class="all-books-section" aria-label="Library Book Catalog">
        <div class="section-title-bar">
            <div>
                <h2 class="section-heading font-serif">
                    {{ $isSearching ? 'Catalog Search Results' : 'All Cataloged Titles' }}
                </h2>
            </div>
            <div class="section-count-badge">
                <span>{{ $books->count() }} of {{ $books->total() }} titles</span>
            </div>
        </div>

        @if ($books->isEmpty())
            <div class="empty-collection-panel">
                <div class="empty-icon-wrap">
                    <x-icon name="open-book" class="w-7 h-7 text-sage" />
                </div>
                <h3 class="empty-title font-serif">No volumes found</h3>
                <p class="empty-description">
                    We could not find any titles matching your current search parameters. Try adjusting your query or resetting your filters.
                </p>
                <div class="empty-actions">
                    @if ($isSearching)
                        <a href="{{ route('books.index') }}" class="btn btn-secondary">
                            <x-icon name="close" class="w-4 h-4" />
                            <span>Clear all filters</span>
                        </a>
                    @endif
                    <a href="{{ route('books.create') }}" class="btn btn-primary">
                        <x-icon name="plus" class="w-4 h-4" />
                        <span>Add first book</span>
                    </a>
                </div>
            </div>
        @else
            <div class="books-editorial-grid">
                @foreach ($books as $book)
                    <article class="editorial-book-card">
                        <a href="{{ route('books.show', $book) }}" class="card-cover-container" tabindex="-1" aria-hidden="true">
                            <x-book-cover :book="$book" />
                        </a>

                        <div class="card-content-body">
                            <div class="card-status-row">
                                <x-status :value="$book->status" />
                                <span class="record-id">#{{ str_pad($book->book_id, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            <h3 class="card-title font-serif">
                                <a href="{{ route('books.show', $book) }}" class="card-title-link">
                                    {{ $book->title }}
                                </a>
                            </h3>

                            <p class="card-author">{{ $book->author }}</p>

                            <div class="card-bottom-row">
                                <div class="card-price-block">
                                    <span class="card-price-label">Valuation</span>
                                    <span class="card-price-value">₱{{ number_format((float) $book->price, 2) }}</span>
                                </div>

                                <a href="{{ route('books.show', $book) }}" class="card-details-btn" title="View details for {{ $book->title }}">
                                    <span>Details</span>
                                    <x-icon name="arrow" class="w-3 h-3 transition-transform group-hover:translate-x-0.5" />
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Preserved Pagination -->
            <div class="pagination-wrapper mt-8">
                {{ $books->links() }}
            </div>
        @endif
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('toggle-trash-btn');
            const closeBtn = document.getElementById('close-trash-btn');
            const drawer = document.getElementById('trash-drawer');

            if (toggleBtn && drawer) {
                toggleBtn.addEventListener('click', () => {
                    const isClosed = drawer.style.display === 'none';
                    drawer.style.display = isClosed ? 'block' : 'none';
                    toggleBtn.setAttribute('aria-expanded', isClosed ? 'true' : 'false');
                    if (isClosed) {
                        drawer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            }

            if (closeBtn && drawer) {
                closeBtn.addEventListener('click', () => {
                    drawer.style.display = 'none';
                    toggleBtn?.setAttribute('aria-expanded', 'false');
                });
            }
        });
    </script>
@endsection
