@extends('layouts.app')
@section('title', $q ? __('":q" için arama sonuçları', ['q' => $q]) : __('Sitede ara'))
@section('content')
<header class="page-hero">
  <div class="container">
    <h1>{{ __('Sitede ara') }}</h1>
    <form class="search" role="search" action="{{ lroute('search') }}" style="margin-top:20px">
      <svg aria-hidden="true"><use href="#i-search"/></svg>
      <label class="visually-hidden" for="q">{{ __('Aranacak kelime') }}</label>
      <input id="q" type="search" name="q" value="{{ $q }}" placeholder="{{ __('Hastalık, belirti, tedavi…') }}" autofocus>
      <button type="submit">{{ __('Ara') }}</button>
    </form>
  </div>
</header>
<section class="section" style="padding-top:40px">
  <div class="container" style="max-width:900px;margin-inline:auto">
    @if ($q && mb_strlen($q) < 2)
      <p class="empty">{{ __('Lütfen en az 2 harf yazın.') }}</p>
    @elseif ($q)
      <p style="color:var(--ink-2)">{{ __(':n sonuç bulundu.', ['n' => $results->count()]) }}</p>
      @if ($results->isEmpty())
        <div class="empty">
          <p>{{ __('Aradığınızı bulamadık. Farklı bir kelime deneyin ya da sorunuzu doğrudan uzmanlarımıza sorun.') }}</p>
          <a class="btn btn--primary" href="{{ lroute('ask.create') }}">{{ __('Uzmana sorun') }}</a>
        </div>
      @else
        <ul class="results">
          @foreach ($results as $r)
            <li><span class="tag">{{ $r['type'] }}</span><br><a href="{{ $r['url'] }}">{{ $r['title'] }}</a><p>{{ $r['text'] }}</p></li>
          @endforeach
        </ul>
      @endif
    @endif
  </div>
</section>
@endsection
