@extends('layouts.app')
@section('title', __('Sayfa bulunamadı'))
@section('content')
<div class="container result-card">
  <div class="badge badge--info"><svg><use href="#i-search"/></svg></div>
  <h1 style="font-size:clamp(2rem,4vw,2.8rem)">{{ __('Aradığınız sayfayı bulamadık') }}</h1>
  <p style="font-size:1.15rem;color:var(--ink-2)">{{ __('Sitemizi yeniledik; bazı sayfaların adresi değişmiş olabilir. Aramayı deneyin ya da ana sayfadan devam edin.') }}</p>
  <form class="search" role="search" action="{{ lroute('search') }}" style="margin:28px auto">
    <svg aria-hidden="true"><use href="#i-search"/></svg>
    <label class="visually-hidden" for="q404">{{ __('Sitede ara') }}</label>
    <input id="q404" type="search" name="q" placeholder="{{ __('Hastalık, belirti, tedavi…') }}">
    <button type="submit">{{ __('Ara') }}</button>
  </form>
  <a class="btn btn--outline" href="{{ lroute('home') }}">{{ __('Ana sayfaya dönün') }}</a>
</div>
@endsection
