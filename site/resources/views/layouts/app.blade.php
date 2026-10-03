@php
    $locale = app()->getLocale();
    $pageTitle = trim($__env->yieldContent('title'));
    $siteName = __('Lösemi Lenfoma Miyelom Derneği');
    $fullTitle = $pageTitle ? $pageTitle.' — '.$siteName : $siteName;
    $description = trim($__env->yieldContent('description')) ?: __('Lösemi, lenfoma ve miyelomla yaşayan insanlar ve aileleri için güvenilir bilgi, destek ve dayanışma.');
    $alternates = $alternates ?? ['tr' => route('tr.home'), 'en' => route('en.home')];
@endphp
<!doctype html>
<html lang="{{ $locale }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 160) }}">
<link rel="canonical" href="{{ url()->current() }}">
@foreach ($alternates as $l => $href)
<link rel="alternate" hreflang="{{ $l }}" href="{{ $href }}">
@endforeach
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $pageTitle ?: $siteName }}">
<meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 200) }}">
<meta property="og:image" content="{{ trim($__env->yieldContent('og_image')) ?: asset('assets/img/logo.png') }}">
<meta property="og:locale" content="{{ $locale === 'en' ? 'en_GB' : 'tr_TR' }}">
<meta name="theme-color" content="#005495">
<link rel="icon" href="{{ asset('assets/img/logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=Figtree:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v={{ filemtime(public_path('assets/css/site.css')) }}">
@stack('head')
</head>
<body>
@include('partials.icons')
@include('partials.header', ['active' => trim($__env->yieldContent('nav'))])
<main id="main">
@yield('content')
</main>
@include('partials.footer')
<script src="{{ asset('assets/js/site.js') }}?v={{ filemtime(public_path('assets/js/site.js')) }}"></script>
@stack('scripts')
</body>
</html>
