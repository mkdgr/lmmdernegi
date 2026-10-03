@extends('layouts.app')
@section('title', (string) $story->title)
@section('description', (string) $story->quote)
@section('nav', 'etkinlik')
@section('content')
<x-page-hero :title="$story->title" :crumbs="[__('Hikâyeler') => lroute('stories.index')]">
  <div class="page-hero__meta"><span class="tag tag--orange">{{ $story->kindLabel() }}</span><span>{{ $story->person_name }}@if($story->condition) · {{ $story->condition }}@endif</span></div>
</x-page-hero>
<div class="container post-layout">
  <article class="prose">
    <x-lang-note :model="$story" />
    @if ($story->quote)<blockquote>"{{ $story->quote }}"</blockquote>@endif
    {!! $story->getTranslation('body', app()->getLocale(), false) ?: $story->getTranslation('body', 'tr', false) !!}
    @if ($story->published_at)<p style="color:var(--ink-3);font-size:.95rem">{{ tr_date($story->published_at) }}</p>@endif
  </article>
  <aside class="sticky-side">
    @if ($story->photo)<x-photo :src="$story->photo" :alt="$story->person_name" ratio="4 / 5" />@endif
    <div class="side-card">
      <h3>{{ __('Hikâyenizi paylaşın') }}</h3>
      <p>{{ __('Deneyiminiz, bu yolun başındaki birine umut olabilir.') }}</p>
      <a class="btn btn--outline btn--sm" href="{{ lroute('contact.create', ['konu' => 'hikaye']) }}">{{ __('Bize yazın') }}</a>
    </div>
    @if ($more->isNotEmpty())
      <nav class="sidenav"><h2>{{ __('Diğer hikâyeler') }}</h2><ul>@foreach ($more as $m)<li><a href="{{ lroute('stories.show', $m->slugFor()) }}">{{ $m->title }}</a></li>@endforeach</ul></nav>
    @endif
  </aside>
</div>
<x-help-band />
@endsection
