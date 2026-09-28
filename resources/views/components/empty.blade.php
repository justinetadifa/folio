@props(['title', 'message', 'icon' => 'book'])
<div class="empty">
    <div class="empty-icon"><x-icon :name="$icon" /></div>
    <h2 class="text-lg font-semibold">{{ $title }}</h2>
    <p class="text-muted mt-2 mb-5 max-w-md mx-auto leading-relaxed">{{ $message }}</p>{{ $slot }}
</div>
