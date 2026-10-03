@extends('layouts.app')
@section('title', __('Yayınlar'))
@section('description', __('Derneğimizin ücretsiz broşürleri ve LLMBİR Bülten arşivi.'))
@section('nav', 'destek')
@section('content')
<x-page-hero :title="__('Yayınlar')" :lead="__('Hastalar ve yakınları için hazırladığımız broşürler ve derneğimizin süreli yayını LLMBİR Bülten. Tümü ücretsizdir.')" />
<section class="section" style="padding-top:48px">
  <div class="container">
    @foreach (['brochures' => __('Broşürler'), 'reports' => __('Raporlar'), 'bulletins' => __('LLMBİR Bülten arşivi')] as $var => $heading)
      @if ($$var->isNotEmpty())
        <div style="margin-bottom:64px">
          <div class="section__head"><div><h2>{{ $heading }}</h2></div></div>
          <div class="docs">
            @foreach ($$var as $pub)
              @php $url = $pub->url(); @endphp
              <a class="doc {{ $url ? '' : 'doc--soon' }}" href="{{ $url ?: '#' }}" @if($url) target="_blank" rel="noopener" @endif>
                <span class="doc__icon">PDF</span>
                <strong>{{ $pub->title }}</strong>
                <span>@if($pub->issue_no){{ __('Sayı :n', ['n' => $pub->issue_no]) }}@endif @if($pub->published_on) · {{ $pub->published_on->format('Y') }}@endif</span>
                @if ($pub->description)<span>{{ $pub->description }}</span>@endif
              </a>
            @endforeach
          </div>
        </div>
      @endif
    @endforeach
  </div>
</section>
@endsection
