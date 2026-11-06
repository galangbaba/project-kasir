@if ($paginator->hasPages())
    <nav class="pager" aria-label="Halaman">
        @if ($paginator->onFirstPage()) <span class="dis">‹ Sebelumnya</span>
        @else <a href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Sebelumnya</a> @endif
        <span>Hal. {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages()) <a href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya ›</a>
        @else <span class="dis">Berikutnya ›</span> @endif
    </nav>
@endif
