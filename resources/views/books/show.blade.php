@extends('layouts.app')
@section('title', $book->title)
@section('section', 'Books')

@section('content')
    @php
        $titleRaw = $book->title ?? '';
        $titleClean = str_replace(['’', '‘', '`'], "'", strtolower(trim($titleRaw)));

        // Dynamic book theme palettes (filled color backgrounds)
        $bookThemes = [
            // 1. BLUE COVERS
            'call me by your name' => [
                'family' => 'blue',
                'accent' => '#0284c7',
                'accent_rgb' => '2, 132, 199',
                'gradient' => 'linear-gradient(155deg, #0284c7 0%, #0369a1 50%, #0c4a6e 100%)',
                'glow' => 'rgba(2, 132, 199, 0.4)',
                'bg_tint' => 'rgba(2, 132, 199, 0.08)',
                'border_tint' => 'rgba(2, 132, 199, 0.45)',
                'label' => 'Mediterranean Azure',
            ],
            'aristotle and dante discover the secrets of the universe' => [
                'family' => 'blue',
                'accent' => '#2563eb',
                'accent_rgb' => '37, 99, 235',
                'gradient' => 'linear-gradient(155deg, #2563eb 0%, #1d4ed8 50%, #1e3a8a 100%)',
                'glow' => 'rgba(37, 99, 235, 0.4)',
                'bg_tint' => 'rgba(37, 99, 235, 0.08)',
                'border_tint' => 'rgba(37, 99, 235, 0.45)',
                'label' => 'Desert Twilight Blue',
            ],
            'the little prince' => [
                'family' => 'blue',
                'accent' => '#1d4ed8',
                'accent_rgb' => '29, 78, 216',
                'gradient' => 'linear-gradient(155deg, #1d4ed8 0%, #1e40af 50%, #172554 100%)',
                'glow' => 'rgba(29, 78, 216, 0.4)',
                'bg_tint' => 'rgba(29, 78, 216, 0.08)',
                'border_tint' => 'rgba(29, 78, 216, 0.45)',
                'label' => 'Celestial Starry Sky',
            ],
            'the great gatsby' => [
                'family' => 'blue',
                'accent' => '#1e3a5f',
                'accent_rgb' => '30, 58, 95',
                'gradient' => 'linear-gradient(155deg, #1e3a5f 0%, #172554 50%, #0f172a 100%)',
                'glow' => 'rgba(30, 58, 95, 0.4)',
                'bg_tint' => 'rgba(30, 58, 95, 0.08)',
                'border_tint' => 'rgba(212, 175, 55, 0.5)',
                'label' => 'Jazz Age Midnight Navy',
            ],

            // 2. RED COVERS
            'the hunger games' => [
                'family' => 'red',
                'accent' => '#dc2626',
                'accent_rgb' => '220, 38, 38',
                'gradient' => 'linear-gradient(155deg, #dc2626 0%, #b91c1c 50%, #7f1d1d 100%)',
                'glow' => 'rgba(220, 38, 38, 0.4)',
                'bg_tint' => 'rgba(220, 38, 38, 0.08)',
                'border_tint' => 'rgba(220, 38, 38, 0.45)',
                'label' => 'Mockingjay Crimson Fire',
            ],
            'red, white & royal blue' => [
                'family' => 'red',
                'accent' => '#b91c1c',
                'accent_rgb' => '185, 28, 28',
                'gradient' => 'linear-gradient(155deg, #b91c1c 0%, #991b1b 50%, #7f1d1d 100%)',
                'glow' => 'rgba(185, 28, 28, 0.4)',
                'bg_tint' => 'rgba(185, 28, 28, 0.08)',
                'border_tint' => 'rgba(185, 28, 28, 0.45)',
                'label' => 'Royal Crimson & Sapphire',
            ],
            'red white & royal blue' => [
                'family' => 'red',
                'accent' => '#b91c1c',
                'accent_rgb' => '185, 28, 28',
                'gradient' => 'linear-gradient(155deg, #b91c1c 0%, #991b1b 50%, #7f1d1d 100%)',
                'glow' => 'rgba(185, 28, 28, 0.4)',
                'bg_tint' => 'rgba(185, 28, 28, 0.08)',
                'border_tint' => 'rgba(185, 28, 28, 0.45)',
                'label' => 'Royal Crimson & Sapphire',
            ],
            'the art of war' => [
                'family' => 'red',
                'accent' => '#991b1b',
                'accent_rgb' => '153, 27, 27',
                'gradient' => 'linear-gradient(155deg, #b91c1c 0%, #991b1b 50%, #450a0a 100%)',
                'glow' => 'rgba(153, 27, 27, 0.4)',
                'bg_tint' => 'rgba(153, 27, 27, 0.08)',
                'border_tint' => 'rgba(153, 27, 27, 0.45)',
                'label' => 'Imperial Vermilion',
            ],
            'a study in scarlet' => [
                'family' => 'red',
                'accent' => '#b91c1c',
                'accent_rgb' => '185, 28, 28',
                'gradient' => 'linear-gradient(155deg, #b91c1c 0%, #991b1b 50%, #450a0a 100%)',
                'glow' => 'rgba(185, 28, 28, 0.4)',
                'bg_tint' => 'rgba(185, 28, 28, 0.08)',
                'border_tint' => 'rgba(185, 28, 28, 0.45)',
                'label' => 'Victorian Scarlet',
            ],

            // 3. GREEN COVERS
            'the seven husbands of evelyn hugo' => [
                'family' => 'green',
                'accent' => '#059669',
                'accent_rgb' => '5, 150, 105',
                'gradient' => 'linear-gradient(155deg, #059669 0%, #047857 50%, #064e3b 100%)',
                'glow' => 'rgba(5, 150, 105, 0.4)',
                'bg_tint' => 'rgba(5, 150, 105, 0.08)',
                'border_tint' => 'rgba(5, 150, 105, 0.45)',
                'label' => 'Hollywood Emerald Silk',
            ],
            'challengers' => [
                'family' => 'green',
                'accent' => '#65a30d',
                'accent_rgb' => '101, 163, 13',
                'gradient' => 'linear-gradient(155deg, #65a30d 0%, #4d7c0f 50%, #1e3a1f 100%)',
                'glow' => 'rgba(101, 163, 13, 0.4)',
                'bg_tint' => 'rgba(101, 163, 13, 0.08)',
                'border_tint' => 'rgba(101, 163, 13, 0.45)',
                'label' => 'Centre Court Lime',
            ],
            'the secret garden' => [
                'family' => 'green',
                'accent' => '#166534',
                'accent_rgb' => '22, 101, 52',
                'gradient' => 'linear-gradient(155deg, #166534 0%, #14532d 50%, #052e16 100%)',
                'glow' => 'rgba(22, 101, 52, 0.4)',
                'bg_tint' => 'rgba(22, 101, 52, 0.08)',
                'border_tint' => 'rgba(22, 101, 52, 0.45)',
                'label' => 'Enchanted Ivy Garden',
            ],

            // 4. GOLD / AMBER / BRONZE COVERS
            'the song of achilles' => [
                'family' => 'amber',
                'accent' => '#d97706',
                'accent_rgb' => '217, 119, 6',
                'gradient' => 'linear-gradient(155deg, #d97706 0%, #b45309 50%, #78350f 100%)',
                'glow' => 'rgba(217, 119, 6, 0.4)',
                'bg_tint' => 'rgba(217, 119, 6, 0.08)',
                'border_tint' => 'rgba(217, 119, 6, 0.45)',
                'label' => 'Spartan Grecian Gold',
            ],
            'noli me tangere' => [
                'family' => 'amber',
                'accent' => '#b45309',
                'accent_rgb' => '180, 83, 9',
                'gradient' => 'linear-gradient(155deg, #b45309 0%, #92400e 50%, #451a03 100%)',
                'glow' => 'rgba(180, 83, 9, 0.4)',
                'bg_tint' => 'rgba(180, 83, 9, 0.08)',
                'border_tint' => 'rgba(180, 83, 9, 0.45)',
                'label' => 'Philippine Antique Ochre',
            ],
            'el filibusterismo' => [
                'family' => 'amber',
                'accent' => '#854d0e',
                'accent_rgb' => '133, 77, 14',
                'gradient' => 'linear-gradient(155deg, #854d0e 0%, #713f12 50%, #422006 100%)',
                'glow' => 'rgba(133, 77, 14, 0.4)',
                'bg_tint' => 'rgba(133, 77, 14, 0.08)',
                'border_tint' => 'rgba(133, 77, 14, 0.45)',
                'label' => 'Propaganda Dark Umber',
            ],
            'the adventures of tom sawyer' => [
                'family' => 'amber',
                'accent' => '#b45309',
                'accent_rgb' => '180, 83, 9',
                'gradient' => 'linear-gradient(155deg, #b45309 0%, #a16207 50%, #713f12 100%)',
                'glow' => 'rgba(180, 83, 9, 0.4)',
                'bg_tint' => 'rgba(180, 83, 9, 0.08)',
                'border_tint' => 'rgba(180, 83, 9, 0.45)',
                'label' => 'Mississippi River Amber',
            ],

            // 5. PURPLE / PLUM / LILAC COVERS
            'pride and prejudice' => [
                'family' => 'purple',
                'accent' => '#7c3aed',
                'accent_rgb' => '124, 58, 237',
                'gradient' => 'linear-gradient(155deg, #7c3aed 0%, #6d28d9 50%, #4c1d95 100%)',
                'glow' => 'rgba(124, 58, 237, 0.4)',
                'bg_tint' => 'rgba(124, 58, 237, 0.08)',
                'border_tint' => 'rgba(124, 58, 237, 0.45)',
                'label' => 'Regency English Lavender',
            ],
            "a room of one's own" => [
                'family' => 'purple',
                'accent' => '#6d28d9',
                'accent_rgb' => '109, 40, 217',
                'gradient' => 'linear-gradient(155deg, #6d28d9 0%, #5b21b6 50%, #2e1065 100%)',
                'glow' => 'rgba(109, 40, 217, 0.4)',
                'bg_tint' => 'rgba(109, 40, 217, 0.08)',
                'border_tint' => 'rgba(109, 40, 217, 0.45)',
                'label' => 'Bloomsbury Intellectual Plum',
            ],
            'a room of ones own' => [
                'family' => 'purple',
                'accent' => '#6d28d9',
                'accent_rgb' => '109, 40, 217',
                'gradient' => 'linear-gradient(155deg, #6d28d9 0%, #5b21b6 50%, #2e1065 100%)',
                'glow' => 'rgba(109, 40, 217, 0.4)',
                'bg_tint' => 'rgba(109, 40, 217, 0.08)',
                'border_tint' => 'rgba(109, 40, 217, 0.45)',
                'label' => 'Bloomsbury Intellectual Plum',
            ],
            'little women' => [
                'family' => 'purple',
                'accent' => '#9d174d',
                'accent_rgb' => '157, 23, 77',
                'gradient' => 'linear-gradient(155deg, #9d174d 0%, #831843 50%, #500724 100%)',
                'glow' => 'rgba(157, 23, 77, 0.4)',
                'bg_tint' => 'rgba(157, 23, 77, 0.08)',
                'border_tint' => 'rgba(157, 23, 77, 0.45)',
                'label' => 'Orchard House Cranberry',
            ],

            // 6. MIDNIGHT & STEAMPUNK COVERS
            'the invisible life of addie larue' => [
                'family' => 'midnight',
                'accent' => '#475569',
                'accent_rgb' => '71, 85, 105',
                'gradient' => 'linear-gradient(155deg, #334155 0%, #1e293b 50%, #020617 100%)',
                'glow' => 'rgba(71, 85, 105, 0.4)',
                'bg_tint' => 'rgba(71, 85, 105, 0.08)',
                'border_tint' => 'rgba(212, 175, 55, 0.5)',
                'label' => 'Constellation Midnight & Gold',
            ],
            'the invisible life of adie larue' => [
                'family' => 'midnight',
                'accent' => '#475569',
                'accent_rgb' => '71, 85, 105',
                'gradient' => 'linear-gradient(155deg, #334155 0%, #1e293b 50%, #020617 100%)',
                'glow' => 'rgba(71, 85, 105, 0.4)',
                'bg_tint' => 'rgba(71, 85, 105, 0.08)',
                'border_tint' => 'rgba(212, 175, 55, 0.5)',
                'label' => 'Constellation Midnight & Gold',
            ],
            'the time machine' => [
                'family' => 'midnight',
                'accent' => '#4338ca',
                'accent_rgb' => '67, 56, 202',
                'gradient' => 'linear-gradient(155deg, #4338ca 0%, #312e81 50%, #1e1b4b 100%)',
                'glow' => 'rgba(67, 56, 202, 0.4)',
                'bg_tint' => 'rgba(67, 56, 202, 0.08)',
                'border_tint' => 'rgba(67, 56, 202, 0.45)',
                'label' => 'Cosmic Victorian Steampunk',
            ],
        ];

        // Archival fallback by book_id % 6
        $archivalThemes = [
            0 => ['family' => 'amber', 'accent' => '#854d0e', 'accent_rgb' => '133, 77, 14', 'gradient' => 'linear-gradient(155deg, #854d0e 0%, #573105 100%)', 'glow' => 'rgba(133, 77, 14, 0.35)', 'bg_tint' => 'rgba(133, 77, 14, 0.08)', 'border_tint' => 'rgba(133, 77, 14, 0.4)', 'label' => 'Warm Parchment'],
            1 => ['family' => 'green', 'accent' => '#1b3529', 'accent_rgb' => '27, 53, 41', 'gradient' => 'linear-gradient(155deg, #244637 0%, #14281e 100%)', 'glow' => 'rgba(27, 53, 41, 0.35)', 'bg_tint' => 'rgba(27, 53, 41, 0.08)', 'border_tint' => 'rgba(27, 53, 41, 0.4)', 'label' => 'Forest Archive'],
            2 => ['family' => 'blue', 'accent' => '#1e3a5f', 'accent_rgb' => '30, 58, 95', 'gradient' => 'linear-gradient(155deg, #1e3a5f 0%, #0d1b2a 100%)', 'glow' => 'rgba(30, 58, 95, 0.35)', 'bg_tint' => 'rgba(30, 58, 95, 0.08)', 'border_tint' => 'rgba(30, 58, 95, 0.4)', 'label' => 'Oxford Navy'],
            3 => ['family' => 'red', 'accent' => '#991b1b', 'accent_rgb' => '153, 27, 27', 'gradient' => 'linear-gradient(155deg, #991b1b 0%, #4a1c15 100%)', 'glow' => 'rgba(153, 27, 27, 0.35)', 'bg_tint' => 'rgba(153, 27, 27, 0.08)', 'border_tint' => 'rgba(153, 27, 27, 0.4)', 'label' => 'Oxblood Linen'],
            4 => ['family' => 'midnight', 'accent' => '#334155', 'accent_rgb' => '51, 65, 85', 'gradient' => 'linear-gradient(155deg, #334155 0%, #1a1e24 100%)', 'glow' => 'rgba(51, 65, 85, 0.35)', 'bg_tint' => 'rgba(51, 65, 85, 0.08)', 'border_tint' => 'rgba(51, 65, 85, 0.4)', 'label' => 'Slate Buckram'],
            5 => ['family' => 'amber', 'accent' => '#b45309', 'accent_rgb' => '180, 83, 9', 'gradient' => 'linear-gradient(155deg, #b45309 0%, #523014 100%)', 'glow' => 'rgba(180, 83, 9, 0.35)', 'bg_tint' => 'rgba(180, 83, 9, 0.08)', 'border_tint' => 'rgba(180, 83, 9, 0.4)', 'label' => 'Amber Leather'],
        ];

        $theme = $bookThemes[$titleClean] ?? $archivalThemes[$book->book_id % 6];
    @endphp

    <!-- Top Navigation Bar & Archival Breadcrumb -->
    <div class="book-view-topbar">
        <a href="{{ route('books.index') }}" class="back-shelf-link">
            <x-icon name="back" class="w-4 h-4" />
            <span>Back to bookshelf</span>
        </a>

        <div class="topbar-accession-pill">
            <span class="accession-dot"></span>
            <span>Folio Archive · Volume #{{ str_pad($book->book_id, 3, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>

    <!-- Main Book Showcase & Details Grid with Injected CSS Theme Variables -->
    <div class="book-detail-grid" style="--book-accent: {{ $theme['accent'] }}; --book-accent-rgb: {{ $theme['accent_rgb'] }}; --book-glow: {{ $theme['glow'] }}; --book-bg-tint: {{ $theme['bg_tint'] }}; --book-border-tint: {{ $theme['border_tint'] }}; --book-gradient: {{ $theme['gradient'] }};">
        
        <!-- 1. Left Book Showcase Panel (Cinematic Volumetric Rays + 3D Pedestal Stage) -->
        <div class="book-showcase-panel book-showcase-filled">
            <!-- Floating Illuminated Status Gem -->
            <div class="showcase-status-badge">
                <span class="showcase-status-dot {{ $book->status === 'Available' ? 'available' : 'rented' }}"></span>
                <span>{{ $book->status === 'Available' ? 'Available for Loan' : 'Currently on Loan' }}</span>
            </div>

            <!-- The Radiant Volumetric Stage -->
            <div class="showcase-stage showcase-stage-filled">
                <!-- God Rays Beam Layer -->
                <div class="showcase-god-rays" aria-hidden="true"></div>
                <!-- High-Lumen Radial Beam Glow -->
                <div class="showcase-beam-glow" aria-hidden="true"></div>
                <!-- Atmosphere Dust Specks -->
                <div class="showcase-atmosphere" aria-hidden="true"></div>

                <!-- 3D Book Cover Object -->
                <div class="showcase-cover-box">
                    <x-book-cover :book="$book" size="showcase" />
                    <div class="showcase-pedestal-3d" aria-hidden="true"></div>
                </div>
            </div>

            <!-- Showcase Archival Footer Tag -->
            <div class="showcase-footer showcase-footer-filled">
                <div class="showcase-footer-row">
                    <span class="showcase-edition-pill-filled">
                        <span class="showcase-edition-dot-filled"></span>
                        <span>{{ $theme['label'] }} Edition</span>
                    </span>
                    <span class="showcase-catalog-stamp font-serif">#{{ str_pad($book->book_id, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Right Book Details Panel (Editorial Curatorial Dossier) -->
        <div class="book-details-panel">
            <!-- Curatorial Header Ribbon -->
            <div class="dossier-ribbon">
                <div class="dossier-curator-badge" style="background: var(--book-gradient); color: #ffffff;">
                    <x-icon name="sparkles" class="w-3.5 h-3.5 text-brass-light" />
                    <span>Folio Curated Collection · {{ $theme['label'] }}</span>
                </div>
                <span class="dossier-catalog-code">FOLIO-{{ str_pad($book->book_id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>

            <!-- Book Title & Author Masthead -->
            <h1 class="dossier-book-title font-serif">{{ $book->title }}</h1>
            <p class="dossier-author-line">
                Authored by <span class="dossier-author-name">{{ $book->author }}</span> · First published in {{ $book->published_year }}
            </p>

            <!-- Curatorial Excerpt / Reader Hook -->
            @if ($book->description)
                <div class="dossier-quote-box" style="border-left-color: var(--book-accent);">
                    <span class="dossier-quote-symbol font-serif">“</span>
                    <p class="dossier-quote-text font-serif italic">{{ Str::limit($book->description, 160) }}</p>
                </div>
            @endif

            <!-- Archival Lending Ledger Strip (Bespoke 4-Column Passport) -->
            <div class="dossier-ledger-strip">
                <div class="ledger-stat-item">
                    <span class="ledger-stat-label">Rental Valuation</span>
                    <span class="ledger-stat-value font-serif">₱{{ number_format((float) $book->price, 2) }}</span>
                    <span class="ledger-stat-caption">Zero collateral needed</span>
                </div>

                <div class="ledger-stat-divider"></div>

                <div class="ledger-stat-item">
                    <span class="ledger-stat-label">Circulation Status</span>
                    <div class="ledger-status-line">
                        <span class="ledger-status-dot {{ $book->status === 'Available' ? 'available' : 'rented' }}"></span>
                        <span class="ledger-status-text font-semibold" style="color: {{ $book->status === 'Available' ? 'var(--book-accent)' : '#b45309' }};">
                            {{ $book->status === 'Available' ? 'Ready to Borrow' : 'Currently on Loan' }}
                        </span>
                    </div>
                    <span class="ledger-stat-caption">{{ $book->status === 'Available' ? 'Ready for immediate dispatch' : 'Checked out by reader' }}</span>
                </div>

                <div class="ledger-stat-divider"></div>

                <div class="ledger-stat-item">
                    <span class="ledger-stat-label">Catalog Vintage</span>
                    <span class="ledger-stat-value font-serif">{{ $book->published_year }}</span>
                    <span class="ledger-stat-caption">Archive Specimen</span>
                </div>

                <div class="ledger-stat-divider"></div>

                <div class="ledger-stat-item">
                    <span class="ledger-stat-label">Standard Loan</span>
                    <span class="ledger-stat-value font-serif">14 Days</span>
                    <span class="ledger-stat-caption">Desk Renewable</span>
                </div>
            </div>

            <!-- Volume Synopsis Block -->
            <div class="dossier-synopsis-box">
                <h3 class="dossier-synopsis-title">Volume Synopsis & Notes</h3>
                <p class="dossier-synopsis-body">{{ $book->description ?: 'No synopsis has been recorded for this volume yet.' }}</p>
            </div>

            <!-- High-Impact "Borrow This Volume" CTA Section -->
            @if ($book->status === 'Available')
                <div class="borrow-hero-container">
                    <a href="{{ route('rentals.create', ['book_id' => $book->book_id]) }}" class="btn-borrow-hero" style="background: var(--book-gradient);">
                        <div class="btn-borrow-shine"></div>
                        <div class="flex items-center gap-3.5">
                            <div class="btn-borrow-icon-wrap">
                                <x-icon name="rental" class="w-5 h-5 text-white" />
                            </div>
                            <div class="text-left">
                                <div class="btn-borrow-headline font-serif">Borrow This Volume</div>
                                <div class="btn-borrow-subline">Issue official Folio rental slip · Standard 14-day checkout</div>
                            </div>
                        </div>
                        <div class="btn-borrow-arrow-wrap">
                            <span>Borrow volume</span>
                            <x-icon name="arrow" class="w-4 h-4 ml-1.5 transition-transform group-hover:translate-x-1" />
                        </div>
                    </a>

                    <div class="borrow-guarantees-row">
                        <div class="guarantee-item">
                            <x-icon name="check-circle" class="w-3.5 h-3.5 text-emerald-600" />
                            <span>Instant digital slip</span>
                        </div>
                        <div class="guarantee-item">
                            <x-icon name="check-circle" class="w-3.5 h-3.5 text-emerald-600" />
                            <span>Zero security deposit</span>
                        </div>
                        <div class="guarantee-item">
                            <x-icon name="check-circle" class="w-3.5 h-3.5 text-emerald-600" />
                            <span>Preserved in fine condition</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="on-loan-notice-card">
                    <div class="flex items-center gap-3.5">
                        <div class="on-loan-icon-box">
                            <x-icon name="info" class="w-5 h-5 text-amber-700" />
                        </div>
                        <div>
                            <h4 class="font-serif text-sm font-semibold text-ink">Volume is Currently on Loan</h4>
                            <p class="text-xs text-muted mt-0.5">This copy has been checked out by a library patron. Review the rental history ledger below for transaction records.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Curatorial Actions Toolbar (Secondary & Discreet) -->
            <div class="dossier-curator-toolbar">
                <div class="flex items-center gap-3">
                    <a href="{{ route('books.edit', $book) }}" class="btn-curator-action">
                        <x-icon name="edit" class="w-3.5 h-3.5 text-muted" />
                        <span>Edit catalog record</span>
                    </a>

                    @if ($rentals->total() === 0)
                        <form method="POST" action="{{ route('books.destroy', $book) }}"
                            data-confirm="Archive '{{ $book->title }}' to Recently Deleted? You can restore it anytime."
                            data-confirm-label="Archive volume">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-curator-delete">
                                <x-icon name="delete" class="w-3.5 h-3.5" />
                                <span>Delete volume</span>
                            </button>
                        </form>
                    @endif
                </div>

                @if ($rentals->total() > 0)
                    <p class="dossier-history-caption">
                        <x-icon name="info" class="w-3.5 h-3.5 text-muted shrink-0" />
                        <span>{{ $rentals->total() }} transaction {{ Str::plural('record', $rentals->total()) }} preserved</span>
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- 3. Rental History Ledger Section -->
    <section class="rental-ledger-panel">
        <div class="ledger-header">
            <div>
                <h2 class="ledger-title font-serif">Rental history</h2>
                <p class="ledger-subtitle">Every reader, every return.</p>
            </div>
            <span class="text-muted text-xs font-semibold">
                {{ $rentals->total() }} {{ Str::plural('transaction', $rentals->total()) }}
            </span>
        </div>
        @include('rentals._history', ['mode' => 'book'])
    </section>

    {{ $rentals->links() }}
@endsection
