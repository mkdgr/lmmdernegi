@extends('layouts.app')
@section('title', __('Etkinlikler'))
@section('description', __('Hasta buluşmaları, kongreler, sempozyumlar ve canlı yayınlar.'))
@section('nav', 'etkinlik')
@section('content')
<x-page-hero :title="__('Etkinlikler')" :lead="__('Hasta ve hasta yakınlarına yönelik buluşmalar, canlı yayınlar, kongreler ve hekimlere yönelik bilimsel toplantılar.')" />
<section class="section" style="padding-top:48px">
  <div class="container">
    <div class="section__head"><div><span class="kicker">{{ __('Yaklaşan') }}</span><h2>{{ __('Yaklaşan etkinlikler') }}</h2></div></div>
    @if ($upcoming->isEmpty())
      <x-alert type="info" :title="__('Şu an planlanmış bir etkinlik yok.')">{{ __('Yeni etkinliklerden haberdar olmak için bültenimize abone olabilirsiniz.') }}</x-alert>
    @else
      @include('posts._event-list', ['items' => $upcoming])
    @endif

    <div class="section__head" style="margin-top:72px"><div><span class="kicker">{{ __('Arşiv') }}</span><h2>{{ __('Geçmiş etkinlikler') }}</h2></div></div>
    <div class="cards">
      @foreach ($past as $post) @include('posts._card') @endforeach
    </div>
    {{ $past->links() }}
  </div>
</section>
<section class="section" style="padding-top:0"><div class="container">@include('partials.newsletter')</div></section>
@endsection
