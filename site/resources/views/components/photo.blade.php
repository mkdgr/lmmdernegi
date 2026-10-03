@props(['src' => null, 'alt' => '', 'caption' => null, 'ratio' => null])
@php $attrs = $ratio ? $attributes->merge(['style' => '--ar: '.$ratio.';']) : $attributes; @endphp
@if ($src)
  <figure {{ $attrs->merge(['class' => 'photo']) }}>
    <img src="{{ media_url($src) }}" alt="{{ $alt }}" loading="lazy">
  </figure>
@else
  <figure {{ $attrs->merge(['class' => 'photo photo--empty']) }}>
    <figcaption><svg aria-hidden="true"><use href="#i-camera"/></svg>{{ $caption ?? __('Fotoğraf') }}</figcaption>
  </figure>
@endif
