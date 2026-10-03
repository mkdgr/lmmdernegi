@extends('layouts.app')
@section('title', __('Hikâyeler'))
@section('description', __('Lösemi, lenfoma ve miyelomla yaşayan insanlar ve yakınları deneyimlerini anlatıyor.'))
@section('nav', 'etkinlik')
@section('content')
<x-page-hero :title="__('Bu yoldan geçenler anlatıyor')" :lead="__('Hastalarımızın ve yakınlarının kendi kaleminden umut, dayanışma ve gündelik hayat hikâyeleri.')" :crumbs="[]" />
<section class="section" style="padding-top:40px">
  <div class="container">
    <nav class="filters" aria-label="{{ __('Türe göre filtrele') }}">
      <a class="chip" href="{{ lroute('stories.index') }}" @if(! $kind) aria-pressed="true" @endif>{{ __('Tümü') }}</a>
      @foreach (\App\Models\Story::KINDS as $k => $label)
        <a class="chip" href="{{ lroute('stories.index', ['tur' => $k]) }}" @if($kind === $k) aria-pressed="true" @endif>{{ __($label) }}</a>
      @endforeach
    </nav>
    <div class="stories" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr))">
      @forelse ($stories as $i => $s)
        <a class="story {{ ['story--sky', 'story--mint', 'story--feature'][$i % 3] }}" href="{{ lroute('stories.show', $s->slugFor()) }}" style="min-height:300px">
          <span class="story__tag">{{ $s->kindLabel() }}</span>
          <blockquote style="font-size:1.25rem">"{{ \Illuminate\Support\Str::limit($s->quote ?: strip_tags($s->body), 170) }}"</blockquote>
          <div class="story__who"><span class="story__avatar">{{ $s->initials() }}</span><span><b>{{ $s->person_name }}</b>{{ $s->condition }}</span></div>
        </a>
      @empty
        <p class="empty">{{ __('Henüz hikâye yok.') }}</p>
      @endforelse
      <a class="story story--sky" href="{{ lroute('contact.create', ['konu' => 'hikaye']) }}" style="min-height:300px">
        <span class="story__tag">{{ __('Sizin hikâyeniz') }}</span>
        <blockquote style="font-size:1.25rem">{{ __('Deneyiminiz, bu yolun başındaki birine umut olabilir. Hikâyenizi bizimle paylaşmak ister misiniz?') }}</blockquote>
        <div class="story__who"><span class="story__avatar">+</span><span><b>{{ __('Hikâyenizi paylaşın') }}</b>{{ __('Bize yazın, birlikte hazırlayalım') }}</span></div>
      </a>
    </div>
    {{ $stories->links() }}
  </div>
</section>
@endsection
