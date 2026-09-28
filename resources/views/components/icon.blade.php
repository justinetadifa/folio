@props(['name'])
<svg {{ $attributes->class(['icon']) }} width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"
    stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('book')
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M6.5 3H20v19H6.5A2.5 2.5 0 0 1 4 19.5v-14A2.5 2.5 0 0 1 6.5 3Z" />
            <path d="M8 7h8M8 11h6" />
        @break

        @case('open-book')
            <path d="M12 5C8 2 4 3 2 4v15c3-1 6-1 10 1 4-2 7-2 10-1V4c-2-1-6-2-10 1Zm0 0v15" />
        @break

        @case('grid')
            <rect x="3" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="3" width="7" height="7" rx="1" />
            <rect x="3" y="14" width="7" height="7" rx="1" />
            <rect x="14" y="14" width="7" height="7" rx="1" />
        @break

        @case('users')
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
            <circle cx="9" cy="7" r="4" />
        @break

        @case('rental')
            <path d="M4 7h15m-4-4 4 4-4 4M20 17H5m4-4-4 4 4 4" />
        @break

        @case('plus')
            <path d="M12 5v14M5 12h14" />
        @break

        @case('arrow')
            <path d="M5 12h14m-5-5 5 5-5 5" />
        @break

        @case('back')
            <path d="M19 12H5m5-5-5 5 5 5" />
        @break

        @case('search')
            <circle cx="10.5" cy="10.5" r="6.5" />
            <path d="m16 16 5 5" />
        @break

        @case('check')
            <path d="m5 12 4 4L19 6" />
        @break

        @case('check-circle')
            <circle cx="12" cy="12" r="9" />
            <path d="m8 12 3 3 5-6" />
        @break

        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2" />
            <path d="M16 3v4M8 3v4M3 11h18" />
        @break

        @case('edit')
            <path d="m15 5 4 4M4 20l4-1L20 7a2.8 2.8 0 0 0-4-4L4 15l-1 6Z" />
        @break

        @case('phone')
            <path d="M7 3H4a1 1 0 0 0-1 1c0 10 7 17 17 17a1 1 0 0 0 1-1v-3l-5-2-2 2a14 14 0 0 1-7-7l2-2-2-5Z" />
        @break

        @case('info')
            <circle cx="12" cy="12" r="9" />
            <path d="M12 11v6M12 7h.01" />
        @break

        @case('menu')
            <path d="M4 6h16M4 12h16M4 18h16" />
        @break

        @case('chevron')
            <path d="m9 5 7 7-7 7" />
        @break

        @case('delete')
            <path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7M14 10v7" />
        @break

        @case('leaf')
            <path d="M20 3C7 2 2 8 5 16c8 3 14-2 15-13ZM4 21 15 10" />
        @break

        @case('sparkles')
            <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3Z" />
        @break

        @case('bookmark')
            <path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z" />
        @break

        @case('filter')
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
        @break

        @case('layers')
            <polygon points="12 2 2 7 12 12 22 7 12 2" />
            <polyline points="2 17 12 22 22 17" />
            <polyline points="2 12 12 17 22 12" />
        @break

        @case('close')
            <path d="M18 6 6 18M6 6l12 12" />
        @break

        @case('rotate-ccw')
            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
            <path d="M3 3v5h5" />
        @break

        @case('history')
            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
            <path d="M3 3v5h5" />
            <path d="M12 7v5l3 3" />
        @break
    @endswitch
</svg>
