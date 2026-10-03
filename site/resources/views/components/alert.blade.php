@props(['type' => 'ok', 'title' => null])
<div {{ $attributes->merge(['class' => 'alert alert--'.$type]) }} role="{{ $type === 'error' ? 'alert' : 'status' }}">
  <svg aria-hidden="true"><use href="#i-{{ $type === 'error' ? 'alert' : ($type === 'info' ? 'info' : 'check') }}"/></svg>
  <div>@if($title)<strong>{{ $title }}</strong>@endif {{ $slot }}</div>
</div>
