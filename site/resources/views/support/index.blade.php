@extends('layouts.app')
@section('title', __('Size destek'))
@section('description', __('Endişelendiğiniz andan tedavi sonrasına kadar her aşamada hastalar ve yakınları için rehberler.'))
@section('nav', 'destek')
@section('content')
<x-page-hero :title="__('Size destek')" :lead="__('Endişelendiğiniz ilk günden tedavi sonrasına kadar, her aşamada yanınızdayız. Bulunduğunuz yeri seçin ya da konu rehberlerine göz atın.')" />

@if ($journey->isNotEmpty())
<section class="section" style="padding-bottom:40px">
  <div class="container">
    <div class="section__head"><div><span class="kicker">{{ __('Size özel bilgi') }}</span><h2>{{ __('Şu an neredesiniz?') }}</h2></div></div>
    <nav class="journey" aria-label="{{ __('Hastalık sürecine göre bilgi') }}">
      @foreach ($journey as $i => $g)
        <a href="{{ lroute('support.show', $g->slugFor()) }}"><span class="journey__num">{{ sprintf('%02d', $i + 1) }}</span><h3>{{ $g->title }}</h3><p>{{ $g->summary }}</p><span class="journey__go"><svg><use href="#i-arrow"/></svg></span></a>
      @endforeach
    </nav>
  </div>
</section>
@endif

@if ($guides->isNotEmpty())
<section class="section" style="padding-top:40px">
  <div class="container">
    <div class="section__head"><div><span class="kicker">{{ __('Konu rehberleri') }}</span><h2>{{ __('Bilmek isteyebilecekleriniz') }}</h2></div></div>
    <div class="guide-grid">
      @foreach ($guides as $g)
        <a class="guide" href="{{ lroute('support.show', $g->slugFor()) }}"><strong>{{ $g->title }}</strong><span>{{ \Illuminate\Support\Str::limit($g->summary, 130) }}</span><i>{{ __('Okuyun') }} →</i></a>
      @endforeach
      <a class="guide" href="{{ lroute('publications.index') }}"><strong>{{ __('Ücretsiz broşürler ve bülten') }}</strong><span>{{ __('Derneğimizin yayınlarını indirip okuyabilirsiniz.') }}</span><i>{{ __('Yayınlar') }} →</i></a>
    </div>
  </div>
</section>
@endif
<x-help-band />
@endsection
