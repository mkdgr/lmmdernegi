@extends('layouts.app')
@section('title', __('Sağlık çalışanları için'))
@section('content')
<x-page-hero :title="__('Sağlık çalışanları için')" :lead="__('Hekimlere ve sağlık çalışanlarına yönelik sempozyumlar, uzman toplantıları ve bilimsel içerikler.')" />
<section class="section" style="padding-top:48px">
  <div class="container">
    @if ($events->isNotEmpty())
      <div class="section__head"><div><span class="kicker">{{ __('Bilimsel toplantılar') }}</span><h2>{{ __('Sempozyum ve uzman toplantıları') }}</h2></div><a class="link-arrow" href="{{ lroute('events.index') }}">{{ __('Tüm etkinlikler') }}</a></div>
      <div class="cards" style="margin-bottom:72px">@foreach ($events as $post) @include('posts._card') @endforeach</div>
    @endif
    @if ($articles->isNotEmpty())
      <div class="section__head"><div><span class="kicker">{{ __('Arşiv') }}</span><h2>{{ __('Bilimsel haberler ve makale özetleri') }}</h2></div></div>
      <ul class="article-list">
        @foreach ($articles as $a)<li><a href="{{ lroute('news.show', $a->slugFor()) }}">{{ $a->title }}</a></li>@endforeach
      </ul>
    @endif
    <div class="help-band" style="margin-top:72px">
      <div><h2>{{ __('Hastalarınızı yönlendirin') }}</h2><p>{{ __('Hastalarınız ve yakınları için ücretsiz broşürlerimiz ve hasta buluşmalarımız var.') }}</p></div>
      <div class="btns"><a class="btn btn--primary" href="{{ lroute('publications.index') }}">{{ __('Broşürler') }}</a><a class="btn btn--outline-white" href="{{ lroute('contact.create') }}">{{ __('Bize ulaşın') }}</a></div>
    </div>
  </div>
</section>
@endsection
