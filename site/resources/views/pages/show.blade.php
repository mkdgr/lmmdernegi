@extends('layouts.app')
@section('title', $page->title)
@section('description', $page->summary)
@section('nav', in_array($page->section, ['rehber', 'destek']) ? 'destek' : ($page->section === 'katilim' ? 'katilim' : 'kurumsal'))
@section('content')
@php
    $body = $page->getTranslation('body', app()->getLocale(), false) ?: $page->getTranslation('body', 'tr', false);
    $crumbs = $crumb ? [$crumb['label'] => $crumb['url']] : [];
@endphp
<x-page-hero :title="$page->title" :lead="$page->summary" :crumbs="$crumbs" />

<div class="container post-layout">
  <article class="prose">
    <x-lang-note :model="$page" />
    @if ($page->image)<x-photo :src="$page->image" :alt="$page->title" ratio="16 / 9" style="margin-bottom:28px" />@endif
    {!! $body !!}
    @if ($page->section === 'rehber' || $page->section === 'destek')
      <div class="review-note">
        <svg aria-hidden="true"><use href="#i-shield"/></svg>
        <div>{{ __('Bu sayfa genel bilgilendirme amaçlıdır; hekim muayenesinin yerini tutmaz. Tanı ve tedaviniz için mutlaka hekiminize danışın.') }}</div>
      </div>
    @endif
  </article>

  <aside class="sticky-side">
    @if ($related->isNotEmpty())
      <nav class="sidenav" aria-label="{{ __('İlgili sayfalar') }}">
        <h2>{{ __(\App\Models\Page::SECTIONS[$page->section] ?? 'İlgili sayfalar') }}</h2>
        <ul>
          @foreach ($related as $r)
            <li><a href="{{ in_array($r->section, ['rehber', 'destek']) ? lroute('support.show', $r->slugFor()) : lroute('page', $r->slugFor()) }}">{{ $r->title }}</a></li>
          @endforeach
        </ul>
      </nav>
    @endif
    <div class="help-box">
      <h3>{{ __('Aklınızda bir soru mu var?') }}</h3>
      <p>{{ __('Sorunuzu iletin, gönüllü uzman hekimlerimiz yanıtlasın.') }}</p>
      <a class="btn btn--primary btn--block" href="{{ lroute('ask.create') }}">{{ __('Uzmana sorun') }}</a>
      <a class="help-box__tel" href="{{ phone_href(setting('phone', '0530 156 87 68')) }}"><svg><use href="#i-phone"/></svg>{{ setting('phone', '0530 156 87 68') }}</a>
    </div>
  </aside>
</div>
@endsection
