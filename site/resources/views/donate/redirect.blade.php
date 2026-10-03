<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">
<title>{{ __('Güvenli ödeme sayfasına yönlendiriliyorsunuz') }}</title>
<link rel="stylesheet" href="{{ asset('assets/css/site.css') }}"></head>
<body>
<div class="container result-card">
  <h1 style="font-size:2rem">{{ __('Güvenli ödeme sayfasına yönlendiriliyorsunuz…') }}</h1>
  <p>{{ __('Kart bilgilerinizi Garanti BBVA\'nın güvenli sayfasında gireceksiniz.') }}</p>
  <form id="pay" method="post" action="{{ $action }}">
    @foreach ($fields as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
    <noscript><p>{{ __('Devam etmek için butona basın.') }}</p></noscript>
    <button class="btn btn--primary" type="submit">{{ __('Ödeme sayfasına git') }}</button>
  </form>
</div>
<script>document.getElementById('pay').submit();</script>
</body></html>
