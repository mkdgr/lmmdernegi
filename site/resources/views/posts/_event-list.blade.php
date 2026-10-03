<ul class="events">
  @foreach ($items as $e)
    @php $date = $e->event_starts_at; @endphp
    <li class="event">
      @if ($date)
        <div class="event__date"><strong>{{ $date->format('d') }}</strong><span>{{ $date->locale(app()->getLocale())->translatedFormat('M Y') }}</span></div>
      @else
        <div class="event__date event__date--none"><strong>—</strong><span>{{ $e->published_at?->format('Y') }}</span></div>
      @endif
      <div>
        <span class="tag {{ $e->audience === 'hekim' ? 'tag--green' : ($e->audience === 'hasta' ? 'tag--orange' : '') }}">{{ $e->audience === 'hekim' ? __('Sağlık çalışanları') : ($e->audience === 'hasta' ? __('Hasta ve yakınları') : $e->typeLabel()) }}</span>
        <h3><a href="{{ lroute('news.show', $e->slugFor()) }}">{{ $e->title }}</a></h3>
        @if ($e->location)<p>{{ $e->location }}</p>@elseif ($e->excerpt)<p>{{ \Illuminate\Support\Str::limit($e->excerpt, 120) }}</p>@endif
      </div>
      @if ($e->video_url)
        <a class="btn btn--outline btn--sm" href="{{ $e->video_url }}" rel="noopener" target="_blank"><svg><use href="#i-video"/></svg>{{ __('Kaydı izleyin') }}</a>
      @else
        <a class="btn btn--outline btn--sm" href="{{ lroute('news.show', $e->slugFor()) }}">{{ __('Ayrıntılar') }}</a>
      @endif
    </li>
  @endforeach
</ul>
