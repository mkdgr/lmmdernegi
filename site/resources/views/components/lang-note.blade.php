@props(['model' => null, 'field' => 'body'])
@if (app()->getLocale() === 'en' && $model && blank($model->getTranslation($field, 'en', false)) && filled($model->getTranslation($field, 'tr', false)))
  <div class="lang-note"><svg aria-hidden="true"><use href="#i-info"/></svg><span>This content is currently available in Turkish only. We are working on an English version.</span></div>
@endif
