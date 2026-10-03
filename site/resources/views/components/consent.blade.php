@props(['name' => 'kvkk', 'bag' => 'default'])
@php $err = $errors->getBag($bag)->first($name); @endphp
<label class="check">
  <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name)) required>
  <span>{!! __('<a href=":url" target="_blank">KVKK aydınlatma metnini</a> okudum; kişisel verilerimin bu başvuru kapsamında işlenmesini kabul ediyorum.', ['url' => page_url('kvkk-aydinlatma-metni')]) !!}</span>
</label>
@if ($err)<p class="error-text">{{ $err }}</p>@endif
