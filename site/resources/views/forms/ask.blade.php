@extends('layouts.app')
@section('title', __('Uzmana sorun'))
@section('description', __('Lösemi, lenfoma ve miyelomla ilgili sorularınızı gönüllü uzman hekimlerimize iletin.'))
@section('nav', 'destek')
@section('content')
<x-page-hero :title="__('Uzmana sorun')" :lead="__('Hastalığınız, tedaviniz ya da günlük hayatınızla ilgili sorularınızı yazın. Gönüllü hematoloji uzmanlarımız en kısa sürede e-posta ile yanıtlasın.')" :crumbs="[__('Size destek') => lroute('support.index')]" />
<div class="container form-grid">
  <div class="form-narrow">
    @if (session('sent'))
      <x-alert :title="__('Sorunuz bize ulaştı.')"><p>{{ __('Uzmanlarımız sorunuzu inceleyip e-posta adresinize yanıt verecek. Bu süreç birkaç gün sürebilir.') }}</p></x-alert>
    @endif
    @if ($errors->any())<x-alert type="error" :title="__('Lütfen işaretli alanları kontrol edin.')" />@endif
    <form method="post" action="{{ lroute('ask.store') }}" novalidate>
      @csrf
      <x-honeypot />
      <fieldset>
        <legend>{{ __('Sorunuz') }}</legend>
        <div class="choice-group choice-group--3" role="radiogroup" aria-label="{{ __('Kim için soruyorsunuz?') }}" style="margin-bottom:18px">
          @foreach (\App\Models\Question::RELATIONS as $k => $label)
            <div class="choice"><input type="radio" id="rel-{{ $k }}" name="relation" value="{{ $k }}" @checked(old('relation', 'hasta') === $k)><label for="rel-{{ $k }}">{{ __($label) }}</label></div>
          @endforeach
        </div>
        @error('relation')<p class="error-text">{{ $message }}</p>@enderror
        <x-field name="disease_id" :label="__('Hangi hastalıkla ilgili?')" type="select" optional>
          <option value="">{{ __('Seçiniz') }}</option>
          @foreach ($diseases as $d)
            <option value="{{ $d->id }}" @selected((string) old('disease_id', request('hastalik')) === (string) $d->id)>{{ $d->name }} ({{ $d->displayAbbr() }})</option>
          @endforeach
        </x-field>
        <x-field name="question" :label="__('Sorunuz')" type="textarea" rows="7" required :hint="__('Kimlik numarası gibi kişisel bilgilerinizi yazmayın.')" />
      </fieldset>
      <fieldset>
        <legend>{{ __('Size nasıl ulaşalım?') }}</legend>
        <div class="grid-2">
          <x-field name="name" :label="__('Ad soyad')" autocomplete="name" required />
          <x-field name="email" :label="__('E-posta')" type="email" autocomplete="email" required :hint="__('Yanıtı bu adrese göndereceğiz.')" />
          <x-field name="phone" :label="__('Telefon')" type="tel" autocomplete="tel" optional />
        </div>
        <x-consent />
      </fieldset>
      <button class="btn btn--primary" type="submit" style="min-height:58px;padding-inline:2em">{{ __('Sorumu gönder') }}</button>
    </form>
  </div>
  <aside>
    <div class="side-card side-card--blue">
      <h3>{{ __('Acil bir durum mu?') }}</h3>
      <p>{{ __('Yüksek ateş, kanama ya da nefes darlığı gibi acil durumlarda beklemeyin: tedavi gördüğünüz merkezi arayın ya da 112\'ye başvurun.') }}</p>
    </div>
    <div class="side-card">
      <h3>{{ __('Bilmeniz gerekenler') }}</h3>
      <ul>
        <li>{{ __('Yanıtlarımız genel bilgilendirme amaçlıdır; muayene ve tedavinin yerini tutmaz.') }}</li>
        <li>{{ __('Sorunuz ve kişisel bilgileriniz gizli tutulur, yalnızca yanıt vermek için kullanılır.') }}</li>
        <li>{!! __('Dilerseniz bizi <a href=":tel">:phone</a> numarasından da arayabilirsiniz.', ['tel' => phone_href(setting('phone', '0530 156 87 68')), 'phone' => setting('phone', '0530 156 87 68')]) !!}</li>
      </ul>
    </div>
  </aside>
</div>
@endsection
