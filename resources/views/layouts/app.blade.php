<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121915">
    <meta name="description"
        content="Folio book rental workspace. An editorial digital library system for readers and librarians.">
    <title>@yield('title', 'Overview') · Folio</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>

<body class="folio-app">
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:bg-cream focus:text-ink focus:px-4 focus:py-2.5 focus:rounded-lg focus:shadow-xl focus:border focus:border-line">
        Skip to main content
    </a>

    <!-- Mobile Navigation Backdrop -->
    <button class="fixed inset-0 bg-ink-deep/60 backdrop-blur-sm z-30 transition-opacity" aria-label="Close navigation" data-backdrop hidden></button>

    @php
        $sidebarTotalBooks = \App\Models\Book::count();
        $sidebarAvailableBooks = \App\Models\Book::where('status', \App\Models\Book::AVAILABLE)->count();
        $sidebarRentedBooks = \App\Models\Book::where('status', \App\Models\Book::RENTED)->count();
    @endphp

    <div class="app-shell">
        <!-- Combined Sidebar / Drawer (Contains Utility Rail + Primary Contextual Sidebar) -->
        <aside class="sidebar-composite" id="sidebar" data-menu>
            <!-- 1. Utility Rail (Leftmost column) -->
            <nav class="utility-rail" aria-label="Utility navigation">
                <!-- Brand Monogram -->
                <a href="{{ route('dashboard') }}" class="rail-brand" aria-label="Folio home" title="Folio · Overview">
                    <span class="rail-monogram-box">
                        <span class="rail-monogram-letter font-serif">F</span>
                    </span>
                </a>

                <!-- Rail Navigation Icons -->
                <div class="rail-nav-group">
                    <a href="{{ route('dashboard') }}"
                        @class(['rail-btn', 'active' => request()->routeIs('dashboard')])
                        @if (request()->routeIs('dashboard')) aria-current="page" @endif
                        title="Overview">
                        <x-icon name="grid" class="w-5 h-5" />
                        <span class="rail-tooltip">Overview</span>
                    </a>

                    <a href="{{ route('books.index') }}"
                        @class(['rail-btn', 'active' => request()->routeIs('books.*')])
                        @if (request()->routeIs('books.*')) aria-current="page" @endif
                        title="Bookshelf">
                        <x-icon name="book" class="w-5 h-5" />
                        <span class="rail-tooltip">Books</span>
                    </a>

                    <a href="{{ route('borrowers.index') }}"
                        @class(['rail-btn', 'active' => request()->routeIs('borrowers.*')])
                        @if (request()->routeIs('borrowers.*')) aria-current="page" @endif
                        title="Borrowers">
                        <x-icon name="users" class="w-5 h-5" />
                        <span class="rail-tooltip">Borrowers</span>
                    </a>

                    <a href="{{ route('rentals.index') }}"
                        @class(['rail-btn', 'active' => request()->routeIs('rentals.*')])
                        @if (request()->routeIs('rentals.*')) aria-current="page" @endif
                        title="Rentals">
                        <x-icon name="rental" class="w-5 h-5" />
                        <span class="rail-tooltip">Rentals</span>
                    </a>
                </div>

                <!-- Rail Bottom Avatar / Desk Stamp -->
                <div class="rail-footer">
                    <div class="rail-desk-stamp" title="Library Desk · Active" aria-label="Library Desk">
                        <span class="rail-stamp-text">LD</span>
                    </div>
                </div>
            </nav>

            <!-- 2. Primary Library Sidebar (Contextual Panel) -->
            <div class="primary-sidebar" aria-label="Library Section Navigation">
                <div class="primary-sidebar-top">
                    <!-- Brand Wordmark & Tagline -->
                    <div class="sidebar-brand-block">
                        <a href="{{ route('dashboard') }}" class="sidebar-brand-title font-serif">
                            folio<span class="text-brass">.</span>
                        </a>
                        <p class="sidebar-brand-tagline">Desk & Lending Archive</p>
                    </div>

                    <!-- Contextual Navigation Links -->
                    <div class="sidebar-section">
                        <span class="sidebar-section-title">Collection</span>
                        <div class="sidebar-nav">
                            <a href="{{ route('books.index') }}"
                                @class([
                                    'sidebar-link',
                                    'active' => request()->routeIs('books.*') && !request()->filled('status')
                                ])>
                                <span class="sidebar-link-label">All Titles</span>
                                <span class="sidebar-link-num">{{ $sidebarTotalBooks }}</span>
                            </a>

                            <a href="{{ route('books.index', ['status' => 'Available']) }}"
                                @class([
                                    'sidebar-link',
                                    'active' => request()->routeIs('books.*') && request('status') === 'Available'
                                ])>
                                <span class="sidebar-link-label flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400" aria-hidden="true"></span>
                                    <span>Available</span>
                                </span>
                                <span class="sidebar-link-num">{{ $sidebarAvailableBooks }}</span>
                            </a>

                            <a href="{{ route('books.index', ['status' => 'Rented']) }}"
                                @class([
                                    'sidebar-link',
                                    'active' => request()->routeIs('books.*') && request('status') === 'Rented'
                                ])>
                                <span class="sidebar-link-label flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400" aria-hidden="true"></span>
                                    <span>On Loan</span>
                                </span>
                                <span class="sidebar-link-num">{{ $sidebarRentedBooks }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Slender Shelf Ratio Meter -->
                    <div class="sidebar-shelf-status">
                        <div class="shelf-status-meta">
                            <span class="shelf-status-label">Circulation</span>
                            <span class="shelf-status-count">{{ $sidebarAvailableBooks }} of {{ $sidebarTotalBooks }} on shelf</span>
                        </div>
                        <div class="shelf-status-track" aria-hidden="true">
                            <div class="shelf-status-fill bg-emerald-500/80" style="width: {{ $sidebarTotalBooks > 0 ? round(($sidebarAvailableBooks / $sidebarTotalBooks) * 100) : 0 }}%"></div>
                            <div class="shelf-status-fill bg-amber-500/80" style="width: {{ $sidebarTotalBooks > 0 ? round(($sidebarRentedBooks / $sidebarTotalBooks) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Library Callout: "Good stories keep moving." -->
                <div class="sidebar-callout-wrap mt-auto">
                    <div class="sidebar-callout-card">
                        <p class="callout-heading font-serif">“Good stories keep moving.”</p>
                        <p class="callout-body">Issue a volume to its next reader.</p>
                        <a href="{{ route('rentals.create') }}" class="callout-action-btn">
                            <span>Issue rental</span>
                            <x-icon name="arrow" class="w-3.5 h-3.5" />
                        </a>
                    </div>
                    <div class="sidebar-meta-footer">
                        <span>Folio Library System</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- 3. Main Workspace Area -->
        <div class="workspace-area">
            <div class="workspace-card">
                <!-- Workspace Topbar -->
                <header class="workspace-header">
                    <div class="flex items-center gap-3">
                        <button class="mobile-menu-toggle" data-menu-toggle aria-label="Toggle navigation"
                            aria-controls="sidebar" aria-expanded="false">
                            <x-icon name="menu" class="w-5 h-5" />
                        </button>
                        <nav class="breadcrumb-nav" aria-label="Breadcrumb">
                            <span class="text-muted text-xs">The Library</span>
                            <span class="text-muted-light text-xs">/</span>
                            <span class="text-xs font-semibold text-ink">@yield('section', 'Books')</span>
                        </nav>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="header-date-chip">
                            <x-icon name="calendar" class="w-3.5 h-3.5 text-muted" />
                            <span class="text-xs font-medium text-muted-dark">{{ now()->format('M d, Y') }}</span>
                        </div>
                        <div class="header-user-badge" title="Desk Operator">
                            <span class="header-avatar">LD</span>
                            <span class="header-user-label hidden sm:inline">Library Desk</span>
                        </div>
                    </div>
                </header>

                <!-- Workspace Scrollable Body -->
                <main class="workspace-body" id="main-content">
                    @if (session('success'))
                        <div class="alert alert-success" role="status">
                            <x-icon name="check-circle" class="w-5 h-5 shrink-0 mt-0.5 text-emerald-700" />
                            <p class="text-sm font-medium">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-error" role="alert">
                            <x-icon name="info" class="w-5 h-5 shrink-0 mt-0.5 text-red-700" />
                            <div>
                                <p class="font-semibold text-sm">Please review the following:</p>
                                <ul class="list-disc pl-4 mt-1.5 text-xs space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @yield('content')

                    <footer class="folio-footer">
                        <span>Folio · A home for every story.</span>
                        <span class="hidden sm:inline">Academic Book Rental System</span>
                    </footer>
                </main>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal Dialog -->
    <dialog id="confirmation-dialog" class="confirm-dialog" aria-labelledby="confirmation-heading"
        aria-describedby="confirmation-message">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-lg bg-brass-subtle text-brass-dark flex items-center justify-center shrink-0">
                <x-icon name="info" class="w-4 h-4" />
            </div>
            <h2 id="confirmation-heading" class="text-xl font-serif text-ink">Confirm this action</h2>
        </div>
        <p id="confirmation-message" data-confirm-message class="text-muted text-sm leading-relaxed mb-6"></p>
        <div class="flex justify-end gap-3">
            <button type="button" class="btn btn-secondary" data-cancel-confirm autofocus>Cancel</button>
            <button type="button" class="btn btn-primary" data-confirm-button>Confirm</button>
        </div>
    </dialog>
</body>

</html>
