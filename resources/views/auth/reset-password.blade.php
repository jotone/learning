<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width">
  <meta name="robots" content="noindex,nofollow">
  <title>{{ $settings['site_title']->val }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500&display=swap">
  <link rel="stylesheet" href="/css/login.css">

  @if(!empty($settings['header_code']->value ))
    {!! $settings['header_code']->val !!}
  @endif
</head>
<body>
<div class="login-page">
  <div class="content-clear">
    <div class="login-form">

      <div class="logo-wrap">
        @if (!empty($settings['logo_img']->value))
          <picture>
            <img src="{{ asset($settings['logo_img']->val) }}" alt="">
          </picture>
        @endif
      </div>

      <div class="form">
        @if(!empty($errors->all()))
          <ul class="form-errors">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        @endif

        <form action="{{ route('reset.update', $token) }}" method="POST">
          @csrf
          <div class="form-text">
            <p>@lang('passwords.msg')</p>
          </div>
          <div class="form-input">
            <label for="email">@lang('user.fields.email')</label>
            <input
              autofocus=""
              autocomplete="off"
              id="email"
              name="email"
              disabled="disabled"
              type="email"
              value="{{ $email }}"
            >
          </div>

          <div class="form-input">
            <label for="password">@lang('user.password.txt')</label>
            <input
              autocomplete="off"
              id="password"
              name="password"
              required=""
              type="password"
            >
          </div>

          <div class="form-input">
            <label for="confirmation">@lang('user.password.confirmation')</label>
            <input
              autocomplete="off"
              id="confirmation"
              name="confirmation"
              required=""
              type="password"
            >
          </div>

          <div class="form-submit">
            <button type="submit">@lang('passwords.set_btn')</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@if(!empty($settings['footer_code']->value ))
  {!! $settings['footer_code']->val !!}
@endif
</body>
</html>
