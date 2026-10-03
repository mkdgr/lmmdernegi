@php
    $locale = app()->getLocale();
    $phone = setting('phone', '0530 156 87 68');
    $cur = fn ($key) => ($active ?? '') === $key ? ' aria-current=page' : '';
@endphp
<a class="skip-link" href="#main">{{ __('İçeriğe geç') }}</a>

<div class="topbar">
  <div class="container">
    <nav class="topbar__links" aria-label="{{ __('Hızlı bağlantılar') }}">
      <a href="{{ lroute('stories.index') }}">{{ __('Hikâyeler') }}</a>
      <a href="{{ lroute('professionals') }}">{{ __('Sağlık çalışanları için') }}</a>
      <a href="{{ lroute('news.index') }}">{{ __('Haberler') }}</a>
      <a href="{{ lroute('publications.index') }}">{{ __('Yayınlar') }}</a>
      <a href="{{ lroute('contact.create') }}">{{ __('İletişim') }}</a>
    </nav>
    <div class="a11y" role="group" aria-label="{{ __('Yazı boyutu ve kontrast') }}">
      <span>{{ __('Yazı boyutu') }}</span>
      <button type="button" data-fontsize-set="md" aria-label="{{ __('Normal yazı boyutu') }}">A</button>
      <button type="button" data-fontsize-set="lg" aria-label="{{ __('Büyük yazı boyutu') }}" style="font-size:1.1em">A</button>
      <button type="button" data-fontsize-set="xl" aria-label="{{ __('Çok büyük yazı boyutu') }}" style="font-size:1.25em">A</button>
      <button type="button" data-contrast-toggle aria-label="{{ __('Yüksek kontrast') }}" title="{{ __('Yüksek kontrast') }}">◐</button>
    </div>
    <nav class="lang" aria-label="{{ __('Dil seçimi') }}">
      <a href="{{ $alternates['tr'] ?? route('tr.home') }}" lang="tr" hreflang="tr" @if($locale === 'tr') aria-current="true" @endif>TR</a>
      <a href="{{ $alternates['en'] ?? route('en.home') }}" lang="en" hreflang="en" @if($locale === 'en') aria-current="true" @endif>EN</a>
    </nav>
  </div>
</div>

<header class="header">
  <div class="container">
    <a class="brand" href="{{ lroute('home') }}">
      <img src="{{ asset('assets/img/logo.png') }}" alt="" width="58" height="58">
      <span class="brand__name">{!! __('Lösemi Lenfoma<br>Miyelom Derneği') !!}</span>
    </a>

    <div class="nav-backdrop" data-nav-close></div>
    <nav class="nav" id="site-nav" aria-label="{{ __('Ana menü') }}">
      <button class="icon-btn nav-close" type="button" data-nav-close aria-label="{{ __('Menüyü kapat') }}"><svg><use href="#i-close"/></svg></button>
      <ul class="nav__list">
        <li class="nav__item">
          <button class="nav__link" type="button" aria-expanded="false" aria-controls="dd-hastalik"{{ $cur('hastalik') }}>{{ __('Hastalıklar') }} <svg><use href="#i-chevron"/></svg></button>
          <ul class="dropdown dropdown--wide" id="dd-hastalik">
            @foreach ($navDiseases->groupBy('group') as $group => $items)
              <li class="dropdown__head">{{ __(\App\Models\Disease::GROUPS[$group] ?? $group) }}</li>
              @foreach ($items as $d)
                <li><a href="{{ lroute('diseases.show', $d->slugFor()) }}">{{ $d->name }} <small>{{ $d->displayAbbr() }}</small></a></li>
              @endforeach
            @endforeach
            <li><a href="{{ lroute('diseases.index') }}">{{ __('Kan kanserleri hakkında') }} <small>{{ __('Genel bilgiler ve tüm hastalıklar') }}</small></a></li>
          </ul>
        </li>
        <li class="nav__item">
          <button class="nav__link" type="button" aria-expanded="false" aria-controls="dd-destek-al"{{ $cur('destek') }}>{{ __('Size destek') }} <svg><use href="#i-chevron"/></svg></button>
          <ul class="dropdown dropdown--wide" id="dd-destek-al">
            <li class="dropdown__head">{{ __('Şu an neredesiniz?') }}</li>
            @foreach ($navJourney as $g)
              <li><a href="{{ lroute('support.show', $g->slugFor()) }}">{{ $g->title }} @if($g->summary)<small>{{ \Illuminate\Support\Str::limit($g->summary, 48) }}</small>@endif</a></li>
            @endforeach
            <li class="dropdown__head">{{ __('Bize ulaşın') }}</li>
            <li><a href="{{ lroute('ask.create') }}">{{ __('Uzmana sorun') }}</a></li>
            <li><a href="{{ lroute('events.index') }}">{{ __('Hasta buluşmaları') }}</a></li>
            <li><a href="{{ lroute('support.index') }}">{{ __('Tüm rehberler') }}</a></li>
            <li><a href="{{ lroute('publications.index') }}">{{ __('Ücretsiz broşürler') }}</a></li>
          </ul>
        </li>
        <li class="nav__item">
          <button class="nav__link" type="button" aria-expanded="false" aria-controls="dd-etkinlik"{{ $cur('etkinlik') }}>{{ __('Etkinlikler') }} <svg><use href="#i-chevron"/></svg></button>
          <ul class="dropdown" id="dd-etkinlik">
            <li><a href="{{ lroute('events.index') }}">{{ __('Etkinlik takvimi') }} <small>{{ __('Kongreler, hasta buluşmaları, canlı yayınlar') }}</small></a></li>
            <li><a href="{{ lroute('news.index') }}">{{ __('Haberler ve duyurular') }}</a></li>
            <li><a href="{{ lroute('stories.index') }}">{{ __('Hikâyeler') }}</a></li>
            <li><a href="{{ lroute('publications.index') }}">{{ __('LLMBİR Bülten') }}</a></li>
          </ul>
        </li>
        <li class="nav__item">
          <button class="nav__link" type="button" aria-expanded="false" aria-controls="dd-destek-ol"{{ $cur('katilim') }}>{{ __('Destek olun') }} <svg><use href="#i-chevron"/></svg></button>
          <ul class="dropdown" id="dd-destek-ol">
            <li><a href="{{ lroute('donate.create') }}">{{ __('Bağış yapın') }}</a></li>
            <li><a href="{{ lroute('membership.create') }}">{{ __('Üye olun') }}</a></li>
            <li><a href="{{ page_url('kok-hucre-vericisi-olmak') }}">{{ __('Kök hücre vericisi olun') }}</a></li>
            <li><a href="{{ page_url('gonullu-olun') }}">{{ __('Gönüllü olun') }}</a></li>
            <li><a href="{{ lroute('donate.create') }}#banka">{{ __('Banka hesaplarımız') }}</a></li>
          </ul>
        </li>
        <li class="nav__item">
          <button class="nav__link" type="button" aria-expanded="false" aria-controls="dd-hakkimizda"{{ $cur('kurumsal') }}>{{ __('Hakkımızda') }} <svg><use href="#i-chevron"/></svg></button>
          <ul class="dropdown" id="dd-hakkimizda">
            <li><a href="{{ page_url('hakkimizda') }}">{{ __('Derneğimiz') }}</a></li>
            <li><a href="{{ page_url('yonetim-kurulu') }}">{{ __('Yönetim kurulu') }}</a></li>
            <li><a href="{{ page_url('tuzuk') }}">{{ __('Tüzük') }}</a></li>
            <li><a href="{{ page_url('uluslararasi-baglantilar') }}">{{ __('Uluslararası bağlantılar') }}</a></li>
            <li><a href="{{ lroute('contact.create') }}">{{ __('İletişim') }}</a></li>
          </ul>
        </li>
      </ul>
      <div class="nav__mobile-cta" style="display:none">
        <a class="btn btn--blue" href="{{ phone_href($phone) }}"><svg><use href="#i-phone"/></svg>{{ $phone }}</a>
        <a class="btn btn--outline" href="{{ lroute('ask.create') }}"><svg><use href="#i-question"/></svg>{{ __('Uzmana sorun') }}</a>
      </div>
    </nav>

    <div class="header__actions">
      <a class="header__phone" href="{{ phone_href($phone) }}"><svg><use href="#i-phone"/></svg><span><small>{{ __('Bize ulaşın') }}</small><strong>{{ $phone }}</strong></span></a>
      <a class="icon-btn header__search" href="{{ lroute('search') }}" aria-label="{{ __('Sitede ara') }}"><svg><use href="#i-search"/></svg></a>
      <a class="btn btn--primary" href="{{ lroute('donate.create') }}"><span class="hide-sm">{{ __('Bağış yapın') }}</span><span class="show-sm" aria-hidden="true">{{ __('Bağış') }}</span></a>
      <button class="icon-btn nav-toggle" type="button" aria-controls="site-nav" aria-expanded="false"><svg><use href="#i-menu"/></svg><span>{{ __('Menü') }}</span></button>
    </div>
  </div>
</header>
