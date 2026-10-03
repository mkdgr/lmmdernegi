@extends('layouts.app')
@section('title', __('İletişim'))
@section('nav', 'kurumsal')
@section('content')
@php $phone = setting('phone', '0530 156 87 68'); $subject = request('konu') === 'hikaye' ? __('Hikâyemi paylaşmak istiyorum') : null; @endphp
<x-page-hero :title="__('İletişim')" :lead="__('Sorularınız, önerileriniz ya da iş birliği talepleriniz için bize yazın; en kısa sürede dönüş yapalım.')" />
<div class="container form-grid">
  <div class="form-narrow">
    @if (session('sent'))
      <x-alert :title="__('Mesajınız bize ulaştı.')"><p>{{ __('Teşekkür ederiz. En kısa sürede size dönüş yapacağız.') }}</p></x-alert>
    @endif
    @if ($errors->any())<x-alert type="error" :title="__('Lütfen işaretli alanları kontrol edin.')" />@endif
    <form method="post" action="{{ lroute('contact.store') }}" novalidate>
      @csrf
      <x-honeypot />
      <div class="grid-2">
        <x-field name="name" :label="__('Ad soyad')" autocomplete="name" required />
        <x-field name="email" :label="__('E-posta')" type="email" autocomplete="email" required />
        <x-field name="phone" :label="__('Telefon')" type="tel" autocomplete="tel" optional />
        <x-field name="subject" :label="__('Konu')" :value="$subject" optional />
      </div>
      <x-field name="message" :label="__('Mesajınız')" type="textarea" rows="7" required />
      <x-consent />
      <button class="btn btn--primary" type="submit" style="min-height:58px;padding-inline:2em">{{ __('Gönder') }}</button>
    </form>
  </div>
  <aside>
    <div class="side-card side-card--blue">
      <h3>{{ __('Bize ulaşın') }}</h3>
      <p><a style="color:#fff;font-weight:700;font-size:1.3rem;text-decoration:none" href="{{ phone_href($phone) }}">{{ $phone }}</a>@if($hours = setting('phone_hours'))<br>{{ $hours }}@endif</p>
      <p><a style="color:#fff" href="mailto:{{ setting('email', 'info@losemilenfomamiyelom.org') }}">{{ setting('email', 'info@losemilenfomamiyelom.org') }}</a></p>
      <p style="margin:0">{{ setting('address', 'Hoşdere Cad. No: 198/5, Çankaya / Ankara') }}</p>
    </div>
    <div class="side-card">
      <h3>{{ __('Tıbbi bir sorunuz mu var?') }}</h3>
      <p>{{ __('Hastalık ve tedaviyle ilgili sorularınızı "Uzmana sorun" formundan iletirseniz doğrudan hekimlerimize ulaşır.') }}</p>
      <a class="btn btn--outline btn--sm" href="{{ lroute('ask.create') }}">{{ __('Uzmana sorun') }}</a>
    </div>
  </aside>
</div>
@endsection
