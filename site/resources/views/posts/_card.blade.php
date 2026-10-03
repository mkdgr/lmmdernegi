<article class="card">
  @if ($post->image)
    <div class="card__media"><img src="{{ media_url($post->image) }}" alt="" loading="lazy"></div>
  @else
    <div class="card__media card__media--empty"><svg aria-hidden="true"><use href="#i-{{ $post->type === 'etkinlik' ? 'calendar' : 'book' }}"/></svg></div>
  @endif
  <div class="card__body">
    <div class="card__meta">
      <span class="tag {{ $post->type === 'etkinlik' ? 'tag--orange' : ($post->type === 'bilimsel' ? 'tag--green' : '') }}">{{ $post->typeLabel() }}</span>
      @if ($post->event_starts_at)<time datetime="{{ $post->event_starts_at->toDateString() }}">{{ tr_date($post->event_starts_at) }}</time>@endif
    </div>
    <h3><a href="{{ lroute('news.show', $post->slugFor()) }}">{{ $post->title }}</a></h3>
    @if ($post->excerpt)<p>{{ \Illuminate\Support\Str::limit($post->excerpt, 140) }}</p>@endif
  </div>
</article>
