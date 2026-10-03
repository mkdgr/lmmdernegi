@extends('layouts.app')
@section('title', __('Haberler ve duyurular'))
@section('nav', 'etkinlik')
@section('content')
<x-page-hero :title="__('Haberler ve duyurular')" :lead="__('Hasta buluşmaları, kongreler, sempozyumlar, kampanyalar ve dernek duyuruları.')" />
<section class="section" style="padding-top:40px">
  <div class="container">
    <nav class="filters" aria-label="{{ __('Türe göre filtrele') }}">
      <a class="chip" href="{{ lroute('news.index') }}" @if(! $type) aria-pressed="true" @endif>{{ __('Tümü') }}</a>
      @foreach (['etkinlik' => __('Etkinlikler'), 'haber' => __('Haberler'), 'duyuru' => __('Duyurular')] as $k => $label)
        <a class="chip" href="{{ lroute('news.index', ['tur' => $k]) }}" @if($type === $k) aria-pressed="true" @endif>{{ $label }}</a>
      @endforeach
    </nav>
    @if ($posts->isEmpty())
      <p class="empty">{{ __('Henüz içerik yok.') }}</p>
    @else
      <div class="cards">
        @foreach ($posts as $post) @include('posts._card') @endforeach
      </div>
      {{ $posts->links() }}
    @endif
  </div>
</section>
@endsection
