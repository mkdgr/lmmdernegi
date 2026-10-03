@extends('layouts.app')
@section('title', __('Bağış yapın'))
@section('description', __('Lösemi, lenfoma ve miyelom hastalarına destek olmak için online bağış yapın ya da üyelik aidatınızı ödeyin.'))
@section('nav', 'katilim')
@section('content')
@php
    $type = old('type', in_array(request('tur'), array_keys(\App\Models\Donation::TYPES)) ? request('tur') : 'bagis');
    $amount = (int) old('amount', in_array($selected, $amounts) ? $selected : 500);
    $custom = old('custom', in_array($selected, $amounts) ? null : ($selected ?: null));
@endphp
<x-page-hero :title="__('Bir hastanın yanında olun')" :lead="__('Bağışlarınız hasta ve hasta yakınlarına yönelik eğitimler, ücretsiz yayınlar, farkındalık kampanyaları ve hasta haklarının savunulması için kullanılır.')" :crumbs="[__('Destek olun') => lroute('donate.create')]" />

<div class="container layout layout--2">
  <form class="form-card" action="{{ lroute('donate.store') }}" method="post" data-donate novalidate>
    @csrf
    <x-honeypot />
    @unless ($posReady)
      <div class="notice">{{ __('Online ödeme altyapısı şu an yapılandırılıyor. Formu doldurursanız bağış talebiniz kaydedilir ve derneğimiz sizinle iletişime geçer; dilerseniz havale/EFT ile de bağış yapabilirsiniz.') }}</div>
    @endunless
    @if ($errors->any())<x-alert type="error" :title="__('Lütfen işaretli alanları kontrol edin.')" />@endif

    <ol class="steps" aria-label="{{ __('Bağış adımları') }}">
      <li class="is-active" aria-current="step">{{ __('1. Bağış bilgileri') }}</li>
      <li class="is-active">{{ __('2. Kişisel bilgiler') }}</li>
      <li>{{ __('3. Güvenli ödeme') }}</li>
    </ol>

    <fieldset>
      <legend>{{ __('Ne için ödeme yapıyorsunuz?') }}</legend>
      <div class="choice-group choice-group--3">
        @foreach (['bagis' => __('Derneğin çalışmalarına destek'), 'aidat' => __('Yıllık üye aidatı'), 'giris' => __('Yeni üyeler için')] as $k => $sub)
          <div class="choice"><input type="radio" id="t-{{ $k }}" name="type" value="{{ $k }}" data-label="{{ __(\App\Models\Donation::TYPES[$k]) }}" @checked($type === $k)><label for="t-{{ $k }}">{{ __(\App\Models\Donation::TYPES[$k]) }}<span>{{ $sub }}</span></label></div>
        @endforeach
      </div>
    </fieldset>

    <fieldset>
      <legend>{{ __('Sıklık') }}</legend>
      <div class="choice-group choice-group--2">
        <div class="choice"><input type="radio" id="fq-tek" name="frequency" value="tek" data-label="{{ __('Tek seferlik') }}" @checked(old('frequency', 'tek') === 'tek')><label for="fq-tek">{{ __('Tek seferlik') }}</label></div>
        <div class="choice"><input type="radio" id="fq-aylik" name="frequency" value="aylik" data-label="{{ __('Her ay') }}" @checked(old('frequency') === 'aylik')><label for="fq-aylik">{{ __('Her ay düzenli') }}</label></div>
      </div>
      <p class="hint" style="margin-top:10px;color:var(--ink-3);font-size:.9rem" data-show-if="frequency=aylik">{{ __('Düzenli bağışta ilk ödemeniz bugün alınır; sonraki aylar için derneğimiz sizinle iletişime geçerek talimatınızı tamamlar.') }}</p>
    </fieldset>

    <fieldset>
      <legend>{{ __('Tutar') }}</legend>
      <div class="choice-group choice-group--4">
        @foreach ($amounts as $a)
          <div class="choice choice--amount"><input type="radio" id="b{{ $a }}" name="amount" value="{{ $a }}" @checked(! $custom && $amount === $a)><label for="b{{ $a }}">{{ number_format($a, 0, ',', '.') }} ₺</label></div>
        @endforeach
      </div>
      <label class="amount-custom"><span class="visually-hidden">{{ __('Farklı tutar') }}</span><input type="text" inputmode="numeric" name="custom" value="{{ $custom }}" placeholder="{{ __('Farklı bir tutar girin') }}" data-amount-custom><span>₺</span></label>
      @error('custom')<p class="error-text">{{ $message }}</p>@enderror
    </fieldset>

    <fieldset>
      <legend>{{ __('Bağışçı bilgileri') }}</legend>
      <div class="choice-group choice-group--2" style="margin-bottom:22px">
        <div class="choice"><input type="radio" id="dt-b" name="donor_type" value="bireysel" @checked(old('donor_type', 'bireysel') === 'bireysel')><label for="dt-b">{{ __('Bireysel') }}</label></div>
        <div class="choice"><input type="radio" id="dt-k" name="donor_type" value="kurumsal" @checked(old('donor_type') === 'kurumsal')><label for="dt-k">{{ __('Kurumsal') }}</label></div>
      </div>
      <div class="grid-2" data-show-if="donor_type=kurumsal">
        <x-field name="company" :label="__('Kurum adı')" />
        <x-field name="tax_no" :label="__('Vergi no')" optional />
      </div>
      <div class="grid-2">
        <x-field name="name" :label="__('Ad soyad')" autocomplete="name" required />
        <x-field name="tckn" :label="__('T.C. kimlik no')" inputmode="numeric" maxlength="11" autocomplete="off" optional :hint="__('Bağış makbuzu için; şifreli saklanır.')" />
        <x-field name="email" :label="__('E-posta')" type="email" autocomplete="email" required :hint="__('Bağış bilginiz bu adrese gönderilir.')" />
        <x-field name="phone" :label="__('Telefon')" type="tel" autocomplete="tel" optional />
      </div>
      <label class="check"><input type="checkbox" name="anonymous" value="1" @checked(old('anonymous'))> {{ __('Bağışımda adım gizli tutulsun.') }}</label>
      <x-consent />
    </fieldset>

    <button class="btn btn--primary btn--block" type="submit" style="min-height:60px;font-size:1.1rem"><svg><use href="#i-lock"/></svg>{{ $posReady ? __('Güvenli ödeme sayfasına geç') : __('Bağış talebimi gönder') }} · <span data-amount-out>{{ number_format($custom ?: $amount, 0, ',', '.') }} ₺</span></button>
    <div class="trust">
      <span><svg><use href="#i-shield"/></svg>{{ __('3D Secure doğrulama') }}</span>
      <span><svg><use href="#i-lock"/></svg>{{ __('Kart bilgileriniz Garanti BBVA\'nın güvenli sayfasında girilir; derneğimiz kart bilginizi görmez.') }}</span>
    </div>
  </form>

  <aside class="summary">
    <div class="summary__box" aria-live="polite">
      <h2>{{ __('Bağış özetiniz') }}</h2>
      <div class="summary__row"><span>{{ __('Tür') }}</span><strong data-type-out>{{ __(\App\Models\Donation::TYPES[$type]) }}</strong></div>
      <div class="summary__row"><span>{{ __('Sıklık') }}</span><strong data-freq-out>{{ __('Tek seferlik') }}</strong></div>
      <div class="summary__total"><span>{{ __('Toplam') }}</span><strong data-amount-out>{{ number_format($custom ?: $amount, 0, ',', '.') }} ₺</strong></div>
    </div>
    <div class="impact">
      <h3>{{ __('Desteğiniz neye dönüşüyor?') }}</h3>
      <ul>
        <li><b>{{ __('Eğitim') }}</b>{{ __('Hasta ve yakınlarına yönelik bilgilendirme toplantıları ve canlı yayınlar') }}</li>
        <li><b>{{ __('Yayın') }}</b>{{ __('Ücretsiz dağıtılan hastalık rehberleri ve broşürler') }}</li>
        <li><b>{{ __('Farkındalık') }}</b>{{ __('Lenfoma Bitecek, KML Bitecek gibi kampanyalar') }}</li>
        <li><b>{{ __('Savunuculuk') }}</b>{{ __('Hasta haklarının ve tedaviye erişimin takibi') }}</li>
      </ul>
    </div>
    <div class="bank-box" id="banka">
      <h3>{{ __('Havale / EFT ile bağış') }}</h3>
      <p style="margin:0;color:var(--ink-3);font-size:.9rem">{{ __('Alıcı') }}: {{ __('Lösemi Lenfoma Miyelom Derneği') }}</p>
      <dl>
        @foreach ((array) (setting('bank_accounts') ?: []) as $acc)
          <dt>{{ $acc['bank'] ?? '' }}</dt>
          <dd><code>{{ $acc['iban'] ?? '' }}</code><button class="copy-btn" type="button" data-copy="{{ str_replace(' ', '', $acc['iban'] ?? '') }}" data-copied="{{ __('Kopyalandı') }}">{{ __('Kopyala') }}</button></dd>
          @if (! empty($acc['swift']))<dd style="color:var(--ink-3);font-size:.85rem">SWIFT: {{ $acc['swift'] }}</dd>@endif
        @endforeach
      </dl>
      <p style="margin:12px 0 0;font-size:.85rem;color:var(--ink-3)">{{ __('Açıklama kısmına ad soyadınızı ve "bağış" yazmayı unutmayın.') }}</p>
    </div>
  </aside>
</div>
@endsection
