<!DOCTYPE html>
<html lang="en-US">

<head>
  <meta charset="utf-8">
  <title>{{ $settings['site_title']['value'] }}</title>

  @include('email.partials.styles')
</head>

<body>
<div class="email-wrap">
  @include('email.partials.company-logo', ['logo' => $settings['logo_img']])
  <div class="top-part-wrap">
    @if (!empty($content['subject']))
      <div class="header-title">
        <h1>{!! $content['subject'] !!}</h1>
      </div>
    @endif

    @if (!empty($content['body']))
      <div class="header-text">
        <p>
          {!! $content['body'] !!}
        </p>
      </div>
    @endif
  </div>

  <div class="content-wrap">
    <div class="button-wrap">
      <a class="button custom" href="{{ $reset_url }}">
        {{ $variables['link_text'][1] }}
      </a>
    </div>

    <div class="follow-wrap">
      <p>@lang('registration.email_click_link'):</p>
      <a href="{{ $reset_url }}">{{ $reset_url }}</a>
    </div>

    <div class="content-misc">
      {!! $content['footer_text'] !!}
    </div>
  </div>

  @include('email.partials.footer')
</div>
</body>
</html>