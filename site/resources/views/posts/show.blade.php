@extends('layouts.app')
@section('title', $post->title)
@section('description', $post->excerpt ?: $post->title)
@section('og_image', $post->image ? media_url($post->image) : '')
@section('nav', 'etkinlik')
@section('content')
@php
    $body = $post->getTranslation('body', app()->getLocale(), false) ?: $post->getTranslation('body', 'tr', false);
    // Çok görselli eski galeri sayfaları: görselleri ızgaraya al
    $imgCount = substr_count((string) $body, '<img');
    if ($imgCount > 6) {
        preg_match_all('#<img[^>]+src="([^"]+)"[^>]*>#', $body, $m);
        $gallery = $m[1];
        $body = trim(preg_replace('#<p>\s*(<img[^>]+>\s*)+</p>|<img[^>]+>#', '', $body));
    }
    // Afiş zaten görsel olarak gösteriliyorsa gövdedeki aynı görseli tekrar gösterme
    if ($post->image) {
        $body = preg_replace('#<p>\s*<img[^>]+src="'.preg_quote(media_url($post->image), '#').'"[^>]*>\s*</p>|<img[^>]+src="'.preg_quote(media_url($post->image), '#').'"[^>]*>#', '', $body);
    }
    $crumb = $post->type === 'bilimsel' ? [__('Sağlık çalışanları için') => lroute('professionals')] : [__('Etkinlikler') => lroute('events.index')];
@endphp
<x-page-hero :title="$post->title" :lead="$post->excerpt" :crumbs="$crumb">
  <div class="page-hero__meta">
    <span class="tag">{{ $post->typeLabel() }}</span>
    @if ($post->event_starts_at)<span><svg><use href="#i-calendar"/></svg>{{ tr_date($post->event_starts_at, 'j F Y, l') }}</span>@endif
  </div>
</x-page-hero>

<div class="container post-layout">
  <article class="prose">
    <x-lang-note :model="$post" />
    {!! $body !!}
    @if (! empty($gallery))
      <div class="gallery">
        @foreach ($gallery as $src)<a href="{{ $src }}" target="_blank" rel="noopener"><img src="{{ $src }}" alt="" loading="lazy"></a>@endforeach
      </div>
    @endif
    @if (blank(strip_tags($body, '<img>')) && empty($gallery) && ! $post->image)
      <p>{{ __('Bu etkinlikle ilgili ayrıntılı bilgi için bizimle iletişime geçebilirsiniz.') }}</p>
    @endif
  </article>

  <aside class="sticky-side">
    @if ($post->image)
      <a class="poster" href="{{ media_url($post->image) }}" target="_blank" rel="noopener"><img src="{{ media_url($post->image) }}" alt="{{ __('Afiş') }}: {{ $post->title }}"></a>
    @endif
    @if ($post->event_starts_at || $post->location || $post->video_url || $post->registration_url)
      <div class="facts-box">
        @if ($post->event_starts_at)
          <div><svg><use href="#i-calendar"/></svg><span><b>{{ tr_date($post->event_starts_at) }}@if($post->event_ends_at && ! $post->event_ends_at->isSameDay($post->event_starts_at)) – {{ tr_date($post->event_ends_at) }}@endif</b>@if($post->event_starts_at->format('H:i') !== '00:00'){{ $post->event_starts_at->format('H:i') }}@endif</span></div>
        @endif
        @if ($post->location)<div><svg><use href="#i-pin"/></svg><span><b>{{ __('Yer') }}</b>{{ $post->location }}</span></div>@endif
        @if ($post->video_url)<a class="btn btn--blue btn--block" href="{{ $post->video_url }}" target="_blank" rel="noopener"><svg><use href="#i-video"/></svg>{{ $post->isPast() ? __('Kaydı izleyin') : __('Canlı yayına katılın') }}</a>@endif
        @if ($post->registration_url && ! $post->isPast())<a class="btn btn--primary btn--block" href="{{ $post->registration_url }}" target="_blank" rel="noopener">{{ __('Kayıt olun') }}</a>@endif
      </div>
    @endif
    @if ($more->isNotEmpty())
      <nav class="sidenav" aria-label="{{ __('Diğer içerikler') }}">
        <h2>{{ __('Diğer içerikler') }}</h2>
        <ul>@foreach ($more as $m)<li><a href="{{ lroute('news.show', $m->slugFor()) }}">{{ $m->title }}</a></li>@endforeach</ul>
      </nav>
    @endif
  </aside>
</div>
@endsection
