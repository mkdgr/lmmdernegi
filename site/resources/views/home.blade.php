@extends('layouts.app')
@section('nav', 'home')
@section('content')
@php $phone = setting('phone', '0530 156 87 68'); $whatsapp = setting('whatsapp'); @endphp

<section class="hero" aria-labelledby="hero-title">
  <div class="container">
    <div>
      <span class="kicker">{{ __('Lösemi · Lenfoma · Miyelom') }}</span>
      <h1 id="hero-title">{{ __('Bu yolda') }} <span>{{ __('yalnız değilsiniz.') }}</span></h1>
      <p class="hero__lead">{{ __('Hastalığınızla ilgili güvenilir bilgiye ulaşın, sorularınızı uzmanlarımıza sorun, sizinle aynı yoldan geçmiş insanlarla tanışın.') }}</p>
      <form class="search" id="ara" role="search" action="{{ lroute('search') }}">
        <svg aria-hidden="true"><use href="#i-search"/></svg>
        <label class="visually-hidden" for="q">{{ __('Sitede ara') }}</label>
        <input id="q" type="search" name="q" placeholder="{{ __('Hastalık, belirti, tedavi…') }}">
        <button type="submit">{{ __('Ara') }}</button>
      </form>
      <div class="quick">
        <span>{{ __('Sık arananlar:') }}</span>
        @foreach ($diseases->take(3) as $d)
          <a href="{{ lroute('diseases.show', $d->slugFor()) }}">{{ $d->displayAbbr() }}</a>
        @endforeach
        <a href="{{ lroute('search', ['q' => __('beslenme')]) }}">{{ __('Beslenme') }}</a>
        <a href="{{ lroute('search', ['q' => __('enfeksiyon')]) }}">{{ __('Enfeksiyondan korunma') }}</a>
        <a href="{{ page_url('kok-hucre-vericisi-olmak') }}">{{ __('Kök hücre nakli') }}</a>
      </div>
    </div>

    <div class="hero__visual">
      <x-photo :src="setting('home_hero_photo')" :alt="__('Derneğimizin bir hasta buluşmasından')" :caption="__('Hasta buluşmasından bir fotoğraf')" />
      <span class="hero__dot" aria-hidden="true"></span>
      <span class="hero__dot hero__dot--sm" aria-hidden="true"></span>
      <div class="hero__badge"><strong>2011</strong><span>{{ __('yılından beri hastaların yanında') }}</span></div>
    </div>
  </div>
</section>

@if ($journey->isNotEmpty())
<section class="section" aria-labelledby="journey-title">
  <div class="container">
    <div class="section__head">
      <div>
        <span class="kicker">{{ __('Size özel bilgi') }}</span>
        <h2 id="journey-title">{{ __('Şu an neredesiniz?') }}</h2>
      </div>
      <p>{{ __('Herkesin süreci farklı. Bulunduğunuz aşamayı seçin; size en çok lazım olacak bilgileri bir araya getirdik.') }}</p>
    </div>
    <nav class="journey" aria-label="{{ __('Hastalık sürecine göre bilgi') }}">
      @foreach ($journey as $i => $g)
        <a href="{{ lroute('support.show', $g->slugFor()) }}"><span class="journey__num">{{ sprintf('%02d', $i + 1) }}</span><h3>{{ $g->title }}</h3><p>{{ $g->summary }}</p><span class="journey__go"><svg><use href="#i-arrow"/></svg></span></a>
      @endforeach
    </nav>
  </div>
</section>
@endif

<section class="section section--blue" aria-labelledby="talk-title">
  <div class="container talk">
    <div>
      <h2 id="talk-title">{{ __('Konuşmak ister misiniz?') }}</h2>
      <p class="lead">{{ __('Aklınıza takılan bir soru, paylaşmak istediğiniz bir endişe… Ekibimiz ve gönüllü uzman hekimlerimiz size yardımcı olmaya hazır.') }}</p>
    </div>
    <div class="talk__options">
      <a class="talk__option" href="{{ phone_href($phone) }}"><svg><use href="#i-phone"/></svg><strong>{{ __('Bizi arayın') }}</strong><span>{{ $phone }}</span></a>
      @if ($whatsapp)
        <a class="talk__option" href="{{ whatsapp_href($whatsapp) }}" rel="noopener"><svg><use href="#i-whatsapp"/></svg><strong>WhatsApp</strong><span>{{ __('Mesaj yazın, size dönelim') }}</span></a>
      @else
        <a class="talk__option" href="mailto:{{ setting('email', 'info@losemilenfomamiyelom.org') }}"><svg><use href="#i-mail"/></svg><strong>{{ __('E-posta') }}</strong><span>{{ setting('email', 'info@losemilenfomamiyelom.org') }}</span></a>
      @endif
      <a class="talk__option" href="{{ lroute('ask.create') }}"><svg><use href="#i-question"/></svg><strong>{{ __('Uzmana sorun') }}</strong><span>{{ __('Sorunuzu yazın, hekimlerimiz yanıtlasın') }}</span></a>
      <a class="talk__option" href="{{ lroute('events.index') }}"><svg><use href="#i-users"/></svg><strong>{{ __('Hasta buluşmaları') }}</strong><span>{{ __('Benzer yoldan geçenlerle tanışın') }}</span></a>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="cond-title">
  <div class="container">
    <div class="section__head">
      <div>
        <span class="kicker">{{ __('Hastalıklar') }}</span>
        <h2 id="cond-title">{{ __('Hastalığınızı tanıyın') }}</h2>
      </div>
      <a class="link-arrow" href="{{ lroute('diseases.index') }}">{{ __('Kan kanserleri hakkında genel bilgiler') }}</a>
    </div>
    <ul class="conditions">
      @foreach ($diseases as $d)
        <li><a href="{{ lroute('diseases.show', $d->slugFor()) }}"><span class="conditions__abbr">{{ $d->displayAbbr() }}</span><span class="conditions__name">{{ $d->name }}</span><span class="conditions__type">{{ $d->groupLabel() }}</span></a></li>
      @endforeach
      <li><a href="{{ lroute('support.index') }}"><span class="conditions__abbr">+</span><span class="conditions__name">{{ __('Tüm hasta rehberleri') }}</span><span class="conditions__type">{{ __('Rehber') }}</span></a></li>
    </ul>
  </div>
</section>

@if ($stories->isNotEmpty())
<section class="section section--sand" aria-labelledby="stories-title">
  <div class="container">
    <div class="section__head">
      <div>
        <span class="kicker">{{ __('Hikâyeler') }}</span>
        <h2 id="stories-title">{{ __('Bu yoldan geçenler anlatıyor') }}</h2>
      </div>
      <a class="link-arrow" href="{{ lroute('stories.index') }}">{{ __('Tüm hikâyeler') }}</a>
    </div>
    <div class="stories">
      @foreach ($stories as $i => $s)
        <a class="story {{ $i === 0 ? 'story--feature' : ($i === 1 ? 'story--sky' : 'story--mint') }}" href="{{ lroute('stories.show', $s->slugFor()) }}">
          @if ($i === 0 && $s->photo)<x-photo :src="$s->photo" :alt="$s->person_name" />@endif
          <span class="story__tag">{{ $s->kindLabel() }}</span>
          <blockquote>"{{ \Illuminate\Support\Str::limit($s->quote, $i === 0 ? 220 : 160) }}"</blockquote>
          <div class="story__who"><span class="story__avatar">{{ $s->initials() }}</span><span><b>{{ $s->person_name }}</b>{{ $s->condition }}</span></div>
        </a>
      @endforeach
      @if ($stories->count() < 3)
        <a class="story story--{{ $stories->count() === 1 ? 'sky' : 'mint' }}" href="{{ lroute('contact.create', ['konu' => 'hikaye']) }}">
          <span class="story__tag">{{ __('Sizin hikâyeniz') }}</span>
          <blockquote>{{ __('Deneyiminiz, bu yolun başındaki birine umut olabilir. Hikâyenizi bizimle paylaşmak ister misiniz?') }}</blockquote>
          <div class="story__who"><span class="story__avatar">+</span><span><b>{{ __('Hikâyenizi paylaşın') }}</b>{{ __('Bize yazın, birlikte hazırlayalım') }}</span></div>
        </a>
      @endif
    </div>
  </div>
</section>
@endif

@if ($events->isNotEmpty())
<section class="section" aria-labelledby="events-title">
  <div class="container">
    <div class="section__head">
      <div>
        <span class="kicker">{{ __('Etkinlikler') }}</span>
        <h2 id="events-title">{{ __('Birlikte öğreniyoruz') }}</h2>
      </div>
      <a class="link-arrow" href="{{ lroute('events.index') }}">{{ __('Tüm etkinlikler') }}</a>
    </div>
    @include('posts._event-list', ['items' => $events])
  </div>
</section>
@endif

<section class="section section--navy" aria-labelledby="give-title">
  <div class="container give">
    <div>
      <span class="kicker" style="color:var(--orange)">{{ __('Destek olun') }}</span>
      <h2 id="give-title">{{ __('Bir hastanın yanında olmanın birçok yolu var.') }}</h2>
      <p class="lead">{{ __('Bağışlarınız hasta eğitimlerine, ücretsiz yayınlara ve hasta haklarının savunulmasına dönüşüyor.') }}</p>
      <a class="btn btn--primary" href="{{ lroute('donate.create') }}" style="margin-top:12px"><svg><use href="#i-heart"/></svg>{{ __('Bağış yapın') }}</a>
    </div>
    <ul class="give__list">
      <li><a href="{{ lroute('donate.create') }}"><svg><use href="#i-heart"/></svg><span><strong>{{ __('Bağış yapın') }}</strong><span>{{ __('Tek seferlik ya da her ay düzenli') }}</span></span><i>→</i></a></li>
      <li><a href="{{ lroute('membership.create') }}"><svg><use href="#i-users"/></svg><span><strong>{{ __('Üye olun') }}</strong><span>{{ __('Derneğimizin bir parçası olun') }}</span></span><i>→</i></a></li>
      <li><a href="{{ page_url('kok-hucre-vericisi-olmak') }}"><svg><use href="#i-drop"/></svg><span><strong>{{ __('Kök hücre vericisi olun') }}</strong><span>{{ __('Bir hastanın aradığı uygun verici siz olabilirsiniz') }}</span></span><i>→</i></a></li>
      <li><a href="{{ page_url('gonullu-olun') }}"><svg><use href="#i-hands"/></svg><span><strong>{{ __('Gönüllü olun') }}</strong><span>{{ __('Etkinlik ve kampanyalarımızda yer alın') }}</span></span><i>→</i></a></li>
    </ul>
  </div>
</section>

<section class="section" aria-label="{{ __('Bülten') }}">
  <div class="container">
    <div class="facts" aria-label="{{ __('Rakamlarla derneğimiz') }}" style="margin-bottom:72px">
      @foreach ((array) (setting('home_facts') ?: []) as $fact)
        <div class="fact"><strong>{{ $fact['value'] ?? '' }}</strong><span>{{ $fact['label'][app()->getLocale()] ?? $fact['label']['tr'] ?? '' }}</span></div>
      @endforeach
    </div>
    @include('partials.newsletter')
  </div>
</section>
@endsection
