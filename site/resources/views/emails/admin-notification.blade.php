<!doctype html>
<html lang="tr"><body style="font-family:Arial,sans-serif;color:#15202b;line-height:1.5">
<h2 style="color:#06233f">{{ $title }}</h2>
<table cellpadding="8" style="border-collapse:collapse">
@foreach ($fields as $label => $value)
  @if (filled($value))
  <tr><td style="color:#5c6b7a;vertical-align:top;white-space:nowrap">{{ $label }}</td><td style="border-bottom:1px solid #eee">{!! nl2br(e($value)) !!}</td></tr>
  @endif
@endforeach
</table>
@if ($panelUrl)<p><a href="{{ $panelUrl }}" style="background:#005495;color:#fff;padding:10px 16px;border-radius:6px;text-decoration:none">Yönetim panelinde aç</a></p>@endif
<p style="color:#5c6b7a;font-size:12px">Bu e-posta web sitesi tarafından otomatik gönderildi.</p>
</body></html>
