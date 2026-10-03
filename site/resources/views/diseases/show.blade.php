@extends('layouts.app')
@section('title', $disease->name)
@section('description', $disease->summary)
@section('nav', 'hastalik')
@section('content')
@php
    $body = $disease->getTranslation('body', app()->getLocale(), false) ?: $disease->getTranslation('body', 'tr', false);
    // İçindekiler: gövdedeki h2 başlıklarına kimlik ver
    $toc = [];
    $body = preg_replace_callback('#<h2([^>]*)>(.*?)</h2>#su', function ($m) use (&$toc) {
        $text = trim(strip_tags($m[2]));
        $id = \Illuminate\Support\Str::slug(str_replace(['ı', 'İ'], ['i', 'i'], $text)) ?: 'b'.count($toc);
        $toc[] = ['id' => $id, 'text' => $text];
        return '<h2 id="'.$id.'">'.$m[2].'</h2>';
    }, (string) $body);
    $faq = $disease->faqItems();
@endphp
<x-page-hero :title="$disease->name" :lead="$disease->summary" :crumbs="[__('Hastalıklar') => lroute('diseases.index')]">
  <div class="page-hero__meta">
    <span><svg><use href="#i-clock"/></svg>{{ __('Okuma süresi: :n dk', ['n' => max(1, (int) ceil(str_word_count(strip_tags($body)) / 200))]) }}</span>
    @if ($disease->reviewed_by)<span><svg><use href="#i-shield"/></svg>{{ __('Gözden geçiren: :name', ['name' => $disease->reviewed_by]) }}</span>@endif
    <span><svg><use href="#i-calendar"/></svg>{{ __('Son güncelleme: :date', ['date' => tr_date($disease->reviewed_at ?? $disease->updated_at, 'F Y')]) }}</span>
  </div>
</x-page-hero>

<div class="container layout">
  <aside aria-label="{{ __('Hastalıklar') }}">
    <nav class="sidenav">
      <h2>{{ __('Hastalıklar') }}</h2>
      <ul>
        @foreach ($siblings as $s)
          <li><a href="{{ lroute('diseases.show', $s->slugFor()) }}" @if($s->is($disease)) aria-current="page" @endif><b>{{ $s->displayAbbr() }}</b>{{ $s->name }}</a></li>
        @endforeach
      </ul>
    </nav>
  </aside>

  <article class="prose">
    <x-lang-note :model="$disease" />
    {!! $body !!}

    @if ($faq)
      <h2 id="sss">{{ __('Sıkça sorulan sorular') }}</h2>
      <div class="faq">
        @foreach ($faq as $item)
          <details><summary>{{ $item['question'] }}</summary><div>{!! nl2br(e($item['answer'])) !!}</div></details>
        @endforeach
      </div>
    @endif

    <div class="review-note">
      <svg aria-hidden="true"><use href="#i-shield"/></svg>
      <div>{{ __('Bu sayfa genel bilgilendirme amaçlıdır; hekim muayenesinin yerini tutmaz. Tanı ve tedaviniz için mutlaka hekiminize danışın.') }}</div>
    </div>
    <div class="share">
      <button class="btn btn--outline btn--sm" type="button" onclick="window.print()"><svg><use href="#i-print"/></svg>{{ __('Yazdır / PDF') }}</button>
    </div>
  </article>

  <aside class="aside-right toc" aria-label="{{ __('Sayfa içeriği') }}">
    @if (count($toc) > 1)
      <nav>
        <h2>{{ __('Bu sayfada') }}</h2>
        <ol>
          @foreach ($toc as $t)<li><a href="#{{ $t['id'] }}">{{ \Illuminate\Support\Str::limit($t['text'], 48) }}</a></li>@endforeach
          @if ($faq)<li><a href="#sss">{{ __('Sıkça sorulan sorular') }}</a></li>@endif
        </ol>
      </nav>
    @endif
    <div class="help-box">
      <h3>{{ __('Aklınızda bir soru mu var?') }}</h3>
      <p>{{ __('Sorunuzu iletin, gönüllü uzman hekimlerimiz yanıtlasın.') }}</p>
      <a class="btn btn--primary btn--block" href="{{ lroute('ask.create', ['hastalik' => $disease->id]) }}">{{ __('Uzmana sorun') }}</a>
      <a class="help-box__tel" href="{{ phone_href(setting('phone', '0530 156 87 68')) }}"><svg><use href="#i-phone"/></svg>{{ setting('phone', '0530 156 87 68') }}</a>
    </div>
  </aside>
</div>
@endsection
