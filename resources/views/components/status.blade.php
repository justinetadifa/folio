@props(['value'])
<span @class([
    'badge',
    'badge-available' => $value === 'Available',
    'badge-rented' => $value === 'Rented',
    'badge-returned' => $value === 'Returned',
])><span class="badge-dot" aria-hidden="true"></span>{{ $value }}</span>
