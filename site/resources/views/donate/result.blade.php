@extends('layouts.app')
@section('title', __('Bağış sonucu'))
@push('head')<meta name="robots" content="noindex">@endpush
@section('content')
@php $ok = $donation->isPaid(); $pending = $donation->status === 'beklemede'; @endphp
<div class="container result-card">
  @if ($ok)
    <div class="badge badge--ok"><svg><use href="#i-check"/></svg></div>
    <h1 style="font-size:clamp(2rem,4vw,2.8rem)">{{ __('Teşekkür ederiz!') }}</h1>
    <p style="font-size:1.15rem;color:var(--ink-2)">{{ __('Bağışınız başarıyla alındı. Desteğiniz, lösemi, lenfoma ve miyelomla yaşayan insanlara umut olacak.') }}</p>
  @elseif ($pending)
    <div class="badge badge--info"><svg><use href="#i-info"/></svg></div>
    <h1 style="font-size:clamp(2rem,4vw,2.8rem)">{{ __('Bağış talebiniz alındı') }}</h1>
    <p style="font-size:1.15rem;color:var(--ink-2)">{{ __('Online ödeme altyapımız şu an yapılandırılıyor; kartınızdan ödeme alınmadı. Derneğimiz sizinle iletişime geçecek. Dilerseniz aşağıdaki hesaplara havale/EFT yapabilirsiniz.') }}</p>
  @else
    <div class="badge badge--fail"><svg><use href="#i-alert"/></svg></div>
    <h1 style="font-size:clamp(2rem,4vw,2.8rem)">{{ __('Ödeme tamamlanamadı') }}</h1>
    <p style="font-size:1.15rem;color:var(--ink-2)">{{ __('Kartınızdan ödeme alınmadı. Bilgilerinizi kontrol edip yeniden deneyebilir ya da havale/EFT ile bağış yapabilirsiniz.') }}</p>
    @if ($donation->bank_message)<p style="color:var(--ink-3)">{{ __('Bankanın yanıtı') }}: {{ $donation->bank_message }}</p>@endif
  @endif
  <dl>
    <dt>{{ __('Tür') }}</dt><dd>{{ __(\App\Models\Donation::TYPES[$donation->type] ?? $donation->type) }}</dd>
    <dt>{{ __('Tutar') }}</dt><dd>{{ number_format($donation->amount, 0, ',', '.') }} ₺</dd>
    <dt>{{ __('Sipariş no') }}</dt><dd>{{ $donation->order_id }}</dd>
  </dl>
  <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
    @if (! $ok)<a class="btn btn--primary" href="{{ lroute('donate.create') }}#banka">{{ $pending ? __('Banka hesaplarımız') : __('Yeniden deneyin') }}</a>@endif
    <a class="btn btn--outline" href="{{ lroute('home') }}">{{ __('Ana sayfaya dönün') }}</a>
  </div>
</div>
@endsection
