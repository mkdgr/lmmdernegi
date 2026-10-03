@extends('layouts.app')
@section('title', __('Hastalıklar'))
@section('description', __('Lösemi, lenfoma ve miyelom türleri hakkında hastalar ve yakınları için sade dilde bilgiler.'))
@section('nav', 'hastalik')
@section('content')
<x-page-hero :title="__('Kan kanserlerini tanıyın')" :lead="__('Lösemi, lenfoma ve miyelom; kan, kemik iliği ve lenf sisteminden kaynaklanan kanserlerdir. Her birinin seyri ve tedavisi farklıdır. Hastalığınızı seçin, sade bir dille anlatalım.')" />

<section class="section" style="padding-top:56px">
  <div class="container">
    @foreach (\App\Models\Disease::GROUPS as $key => $label)
      @if ($groups->has($key))
        <div style="margin-bottom:56px">
          <span class="kicker">{{ __($label) }}</span>
          <ul class="conditions">
            @foreach ($groups[$key] as $d)
              <li><a href="{{ lroute('diseases.show', $d->slugFor()) }}"><span class="conditions__abbr">{{ $d->displayAbbr() }}</span><span class="conditions__name">{{ $d->name }}</span><span class="conditions__type">{{ \Illuminate\Support\Str::limit($d->summary, 60) }}</span></a></li>
            @endforeach
          </ul>
        </div>
      @endif
    @endforeach

    @if ($intro)
      <div class="guide-grid">
        <a class="guide" href="{{ lroute('support.show', $intro->slugFor()) }}"><strong>{{ $intro->title }}</strong><span>{{ $intro->summary }}</span><i>{{ __('Okuyun') }} →</i></a>
        <a class="guide" href="{{ lroute('support.index') }}"><strong>{{ __('Hasta rehberleri') }}</strong><span>{{ __('Beslenme, enfeksiyondan korunma, hasta hakları ve daha fazlası.') }}</span><i>{{ __('Tüm rehberler') }} →</i></a>
        <a class="guide" href="{{ lroute('ask.create') }}"><strong>{{ __('Uzmana sorun') }}</strong><span>{{ __('Hastalığınızla ilgili sorularınızı gönüllü hekimlerimize iletin.') }}</span><i>{{ __('Soru gönderin') }} →</i></a>
      </div>
    @endif
  </div>
</section>
<x-help-band />
@endsection
