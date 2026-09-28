@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Pagination">
        <p>Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }} records</p>
        <div class="flex gap-1.5 flex-wrap">
            @if ($paginator->onFirstPage())
            <span class="page-button" aria-disabled="true" aria-label="Previous page">‹</span>@else<a
                    class="page-button" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous page">‹</a>
            @endif
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="p-2">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-button" aria-current="page"
                            aria-label="Page {{ $page }}">{{ $page }}</span>@else<a
                                class="page-button" href="{{ $url }}"
                                aria-label="Page {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
            @if ($paginator->hasMorePages())
            <a class="page-button" href="{{ $paginator->nextPageUrl() }}" aria-label="Next page">›</a>@else<span
                    class="page-button" aria-disabled="true" aria-label="Next page">›</span>
            @endif
        </div>
    </nav>
@endif
