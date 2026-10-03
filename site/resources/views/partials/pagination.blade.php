@if ($paginator->hasPages())
<nav class="pagination" aria-label="{{ __('Sayfalar') }}">
  @if (! $paginator->onFirstPage())
    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('Önceki sayfa') }}">‹</a>
  @endif
  @foreach ($elements as $element)
    @if (is_string($element))
      <span aria-hidden="true">…</span>
    @endif
    @if (is_array($element))
      @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
          <span aria-current="page">{{ $page }}</span>
        @else
          <a href="{{ $url }}">{{ $page }}</a>
        @endif
      @endforeach
    @endif
  @endforeach
  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('Sonraki sayfa') }}">›</a>
  @endif
</nav>
@endif
