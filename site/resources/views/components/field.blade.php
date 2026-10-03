@props(['name', 'label', 'type' => 'text', 'optional' => false, 'hint' => null, 'value' => null])
@php $id = 'f-'.str_replace(['[', ']', '.'], '-', $name); $err = $errors->first($name); @endphp
<div class="field">
  <label for="{{ $id }}">{{ $label }} @if($optional)<span class="opt">({{ __('isteğe bağlı') }})</span>@endif</label>
  @if ($type === 'textarea')
    <textarea id="{{ $id }}" name="{{ $name }}" @if($err) aria-invalid="true" aria-describedby="{{ $id }}-err" @endif {{ $attributes }}>{{ old($name, $value) }}</textarea>
  @elseif ($type === 'select')
    <select id="{{ $id }}" name="{{ $name }}" @if($err) aria-invalid="true" aria-describedby="{{ $id }}-err" @endif {{ $attributes }}>{{ $slot }}</select>
  @else
    <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}" @if($err) aria-invalid="true" aria-describedby="{{ $id }}-err" @endif {{ $attributes }}>
  @endif
  @if ($hint)<span class="hint">{{ $hint }}</span>@endif
  @if ($err)<span class="error" id="{{ $id }}-err">{{ $err }}</span>@endif
</div>
