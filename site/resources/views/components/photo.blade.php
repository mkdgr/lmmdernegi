@props(['src' => null, 'alt' => '', 'caption' => null, 'ratio' => null])
@if ($src)
  <figure {{ $attributes->merge(['class' => 'photo']) }} @if($ratio) style="--ar: {{ $ratio }}" @endif>
    <img src="{{ media_url($src) }}" alt="{{ $alt }}" loading="lazy">
  </figure>
@else
  <figure {{ $attributes->merge(['class' => 'photo photo--empty']) }} @if($ratio) style="--ar: {{ $ratio }}" @endif>
    <figcaption><svg aria-hidden="true"><use href="#i-camera"/></svg>{{ $caption ?? __('Fotoğraf') }}</figcaption>
  </figure>
@endif
