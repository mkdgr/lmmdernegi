@php $phone = setting('phone', '0530 156 87 68'); @endphp
<footer class="footer">
  <div class="container">
    <div class="footer__top">
      <div>
        <div class="footer__brand">
          <img src="{{ asset('assets/img/logo.png') }}" alt="" width="64" height="64">
          <strong>{!! __('Lösemi Lenfoma<br>Miyelom Derneği') !!}</strong>
        </div>
        <p>{{ __('2011\'den beri lösemi, lenfoma ve miyelomla yaşayan insanların ve ailelerinin yanındayız.') }}</p>
        <div class="social">
          @foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook', 'x' => 'X', 'youtube' => 'YouTube'] as $key => $label)
            @if ($url = setting('social_'.$key))
              <a href="{{ $url }}" aria-label="{{ $label }}" rel="noopener" target="_blank"><svg><use href="#i-{{ $key }}"/></svg></a>
            @endif
          @endforeach
        </div>
      </div>
      <div>
        <h4>{{ __('Size destek') }}</h4>
        <ul>
          <li><a href="{{ lroute('diseases.index') }}">{{ __('Hastalıklar') }}</a></li>
          <li><a href="{{ lroute('support.index') }}">{{ __('Hasta rehberi') }}</a></li>
          <li><a href="{{ lroute('ask.create') }}">{{ __('Uzmana sorun') }}</a></li>
          <li><a href="{{ lroute('stories.index') }}">{{ __('Hikâyeler') }}</a></li>
          <li><a href="{{ lroute('publications.index') }}">{{ __('Ücretsiz broşürler') }}</a></li>
        </ul>
      </div>
      <div>
        <h4>{{ __('Dernek') }}</h4>
        <ul>
          <li><a href="{{ page_url('hakkimizda') }}">{{ __('Hakkımızda') }}</a></li>
          <li><a href="{{ lroute('events.index') }}">{{ __('Etkinlikler') }}</a></li>
          <li><a href="{{ lroute('donate.create') }}">{{ __('Bağış yapın') }}</a></li>
          <li><a href="{{ lroute('membership.create') }}">{{ __('Üye olun') }}</a></li>
          <li><a href="{{ lroute('professionals') }}">{{ __('Sağlık çalışanları için') }}</a></li>
        </ul>
      </div>
      <div class="footer__help">
        <h4>{{ __('Konuşmak ister misiniz?') }}</h4>
        <a href="{{ phone_href($phone) }}"><strong>{{ $phone }}</strong></a>
        @if ($hours = setting('phone_hours'))<p style="margin:0 0 12px">{{ $hours }}</p>@endif
        <a href="mailto:{{ setting('email', 'info@losemilenfomamiyelom.org') }}">{{ setting('email', 'info@losemilenfomamiyelom.org') }}</a><br>
        <span>{{ setting('address', 'Hoşdere Cad. No: 198/5, Çankaya / Ankara') }}</span>
      </div>
    </div>
    <p class="disclaimer">{{ __('Bu sitedeki bilgiler genel bilgilendirme amaçlıdır; hekim muayenesinin ve tedavisinin yerini tutmaz. Tanı ve tedaviniz için mutlaka hekiminize danışın.') }}</p>
    <div class="footer__bottom">
      <span>© {{ date('Y') }} {{ __('Lösemi Lenfoma Miyelom Derneği') }}</span>
      <ul>
        <li><a href="{{ page_url('kvkk-aydinlatma-metni') }}">{{ __('KVKK aydınlatma metni') }}</a></li>
        <li><a href="{{ page_url('cerez-politikasi') }}">{{ __('Çerez politikası') }}</a></li>
        <li><a href="{{ page_url('erisilebilirlik') }}">{{ __('Erişilebilirlik') }}</a></li>
        <li><a href="{{ url('/admin') }}" rel="nofollow">{{ __('Yönetim') }}</a></li>
      </ul>
    </div>
  </div>
</footer>
