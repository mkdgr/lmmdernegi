<div class="newsletter" id="bulten">
  <div>
    <h2>{{ __('Haberdar olun') }}</h2>
    <p>{{ __('Hasta buluşmaları, canlı yayınlar ve yeni yayınlarımız e-postanızda.') }}</p>
  </div>
  @if (session('newsletter'))
    <x-alert :title="__('Teşekkürler!')">{{ __('Bülten listemize eklendiniz.') }}</x-alert>
  @else
  <form action="{{ lroute('newsletter.store') }}" method="post">
    @csrf
    <x-honeypot />
    <label class="visually-hidden" for="nl-email">{{ __('E-posta adresiniz') }}</label>
    <input id="nl-email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('E-posta adresiniz') }}" autocomplete="email" required>
    <button class="btn btn--navy" type="submit">{{ __('Abone olun') }}</button>
    <label class="consent"><input type="checkbox" name="kvkk" value="1" required> <span>{!! __('<a href=":url">Aydınlatma metnini</a> okudum, bülten gönderimine onay veriyorum.', ['url' => page_url('kvkk-aydinlatma-metni')]) !!}</span></label>
    @if ($errors->newsletter->any())<p class="error-text" style="flex-basis:100%">{{ $errors->newsletter->first() }}</p>@endif
  </form>
  @endif
</div>
