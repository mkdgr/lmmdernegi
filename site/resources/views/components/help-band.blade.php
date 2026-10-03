@php $phone = setting('phone', '0530 156 87 68'); @endphp
<section class="container" style="padding-bottom:80px">
  <div class="help-band">
    <div>
      <h2>{{ __('Aklınızda bir soru mu var?') }}</h2>
      <p>{{ __('Sorunuzu iletin, gönüllü uzman hekimlerimiz yanıtlasın. Ya da bizi arayın.') }}</p>
    </div>
    <div class="btns">
      <a class="btn btn--primary" href="{{ lroute('ask.create') }}"><svg><use href="#i-question"/></svg>{{ __('Uzmana sorun') }}</a>
      <a class="btn btn--outline-white" href="{{ phone_href($phone) }}"><svg><use href="#i-phone"/></svg>{{ $phone }}</a>
    </div>
  </div>
</section>
