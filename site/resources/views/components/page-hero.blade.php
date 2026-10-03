@props(['title', 'lead' => null, 'crumbs' => []])
<header class="page-hero">
  <div class="container">
    <ol class="breadcrumb" aria-label="{{ __('Sayfa yolu') }}">
      <li><a href="{{ lroute('home') }}">{{ __('Ana sayfa') }}</a></li>
      @foreach ($crumbs as $label => $url)
        <li><a href="{{ $url }}">{{ $label }}</a></li>
      @endforeach
      <li><span aria-current="page">{{ \Illuminate\Support\Str::limit($title, 60) }}</span></li>
    </ol>
    <h1>{{ $title }}</h1>
    @if ($lead)<p>{{ $lead }}</p>@endif
    {{ $slot }}
  </div>
</header>
