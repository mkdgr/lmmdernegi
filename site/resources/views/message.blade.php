@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="container result-card">
  <div class="badge badge--info"><svg><use href="#i-info"/></svg></div>
  <h1 style="font-size:clamp(2rem,4vw,2.8rem)">{{ $title }}</h1>
  <p style="font-size:1.15rem;color:var(--ink-2)">{{ $text }}</p>
  <a class="btn btn--navy" href="{{ lroute('home') }}">{{ __('Ana sayfaya dönün') }}</a>
</div>
@endsection
