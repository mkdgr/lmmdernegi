@extends('layouts.app')
@section('title', __('Üye olun'))
@section('nav', 'katilim')
@section('content')
<x-page-hero :title="__('Derneğimize üye olun')" :lead="__('Hastalar, hasta yakınları, sağlık çalışanları ve destekçiler derneğimize üye olabilir. Başvurunuzu aldıktan sonra yönetim kurulumuzun onayıyla üyeliğiniz tamamlanır.')" :crumbs="[__('Destek olun') => lroute('donate.create')]" />
<div class="container form-grid">
  <div class="form-narrow">
    @if (session('sent'))
      <x-alert :title="__('Başvurunuz alındı.')"><p>{{ __('Teşekkür ederiz. Başvurunuz değerlendirildikten sonra sizinle iletişime geçeceğiz.') }}</p></x-alert>
    @endif
    @if ($errors->any())<x-alert type="error" :title="__('Lütfen işaretli alanları kontrol edin.')" />@endif
    <form method="post" action="{{ lroute('membership.store') }}" novalidate>
      @csrf
      <x-honeypot />
      <fieldset>
        <legend>{{ __('Kişisel bilgiler') }}</legend>
        <div class="grid-2">
          <x-field name="name" :label="__('Ad soyad')" autocomplete="name" required />
          <x-field name="tckn" :label="__('T.C. kimlik no')" inputmode="numeric" maxlength="11" autocomplete="off" optional :hint="__('Dernekler mevzuatı gereği üye kaydında istenir; şifreli saklanır.')" />
          <x-field name="birth_date" :label="__('Doğum tarihi')" type="date" optional />
          <x-field name="occupation" :label="__('Meslek')" optional />
        </div>
      </fieldset>
      <fieldset>
        <legend>{{ __('İletişim bilgileri') }}</legend>
        <div class="grid-2">
          <x-field name="email" :label="__('E-posta')" type="email" autocomplete="email" required />
          <x-field name="phone" :label="__('Telefon')" type="tel" autocomplete="tel" required />
          <x-field name="city" :label="__('Şehir')" autocomplete="address-level1" optional />
        </div>
        <x-field name="address" :label="__('Adres')" type="textarea" rows="3" optional />
      </fieldset>
      <fieldset>
        <legend>{{ __('Derneğimizle bağınız') }}</legend>
        <div class="choice-group choice-group--2" style="grid-template-columns:repeat(2,minmax(0,1fr))">
          @foreach (\App\Models\MembershipApplication::RELATIONS as $k => $label)
            <div class="choice"><input type="radio" id="mrel-{{ $k }}" name="relation" value="{{ $k }}" @checked(old('relation') === $k)><label for="mrel-{{ $k }}">{{ __($label) }}</label></div>
          @endforeach
        </div>
        <div style="margin-top:18px"><x-field name="note" :label="__('Eklemek istedikleriniz')" type="textarea" rows="3" optional /></div>
        <x-consent />
      </fieldset>
      <button class="btn btn--primary" type="submit" style="min-height:58px;padding-inline:2em">{{ __('Başvurumu gönder') }}</button>
    </form>
  </div>
  <aside>
    @if ($info && filled(strip_tags((string) $info->body)))
      <div class="side-card prose" style="font-size:1rem">{!! $info->body !!}</div>
    @endif
    <div class="side-card side-card--blue">
      <h3>{{ __('Aidat ödemesi') }}</h3>
      <p>{{ __('Üyelik aidatınızı online bağış sayfamızdan "Üyelik aidatı" seçeneğiyle ödeyebilirsiniz.') }}</p>
      <a class="btn btn--primary btn--sm" href="{{ lroute('donate.create', ['tur' => 'aidat']) }}">{{ __('Aidat öde') }}</a>
    </div>
  </aside>
</div>
@endsection
