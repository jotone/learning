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

        <form class="form-switch" data-trigger="login" action="{{ route('auth.login') }}" method="POST">
          @csrf
          <div class="form-input">
            <label for="email">@lang('profile.email')</label>
            <input
              autofocus=""
              autocomplete="off"
              id="email"
              name="email"
              placeholder="example@email.com"
              required=""
              type="email"
            >
          </div>

          <div class="form-input">
            <label for="password">@lang('profile.password')</label>
            <input
              autocomplete="off"
              id="password"
              name="password"
              required=""
              type="password"
            >
          </div>

          <div class="form-submit">
            <button type="submit">@lang('auth.login')</button>
          </div>
        </form>

        <form class="form-switch" data-trigger="forgot" action="{{ route('reset.send') }}" method="POST">
          @csrf

          <div class="form-input">
            <label for="forgot-email">@lang('profile.email')</label>
            <input autocomplete="off" id="forgot-email" name="email" class="form-input" type="email" required="">
          </div>

          <div class="form-submit">
            <button>@lang('common.submit')</button>
          </div>
        </form>

        <div class="form-optional-link">
          <a href="#" data-target="forgot">@lang('auth.forgot_pwd')</a>
          <a href="#" data-target="login" class="">@lang('common.cancel')</a>
        </div>
      </div>
    </div>
  </div>
</div>

@if(!empty($settings['footer_code']->value ))
  {!! $settings['footer_code']->val !!}
@endif

<script>
  {{--
  document.querySelector('form[data-trigger="' + ('#forgot' === window.location.hash ? 'forgot' : 'login') + '"]').classList.add('active')
  document.querySelector('.form-optional-link a[data-target="' + ('#forgot' === window.location.hash ? 'login' : 'forgot' + '"]')).classList.add('active')
  var switchForm = function (obj) {
    const target = obj.getAttribute('data-target')
    document.querySelector('form.form-switch.active').classList.remove('active')
    document.querySelector(`form[data-trigger="${target}"]`).classList.add('active')
    document.querySelector('.form-optional-link a.active[data-target]').classList.remove('active')
    document.querySelector(`.form-optional-link a:not([data-target="${target}"])`).classList.add('active')
  }
  var controls = document.querySelectorAll('a[data-target]');
  for (var i = 0, n = controls.length; i < n; i++) {
    controls[i].addEventListener('click',function(e){
      e.preventDefault();switchForm(e.target)
    },!1);
  }--}}

  let a = "active", b = "data-target", c = "classList", d = document, e = "data-trigger", f = ".form-optional-link",
    g = "forgot", j = "remove", k = "add", l = "login", q = "querySelector", z = `#${g}` === window.location.hash,
    h = d[`${q}All`](`a[${b}]`), s = o => {
      let t = o.getAttribute(b);
      d[q](`form.form-switch.${a}`)[c][j](a);
      d[q](`form[${e}="${t}"]`)[c][k](a);
      d[q](`${f}>a.${a}[${b}]`)[c][j](a);
      d[q](`${f}>a:not([${b}="${t}"])`)[c][k](a)
    };
  d[q](`form[${e}="${z ? g : l}"]`)[c][k](a);
  d[q](`${f}>a[${b}="${z ? l : g}"]`)[c][k](a);
  for (let i = 0, n = h.length; i < n; i++) h[i].addEventListener("click", E => {
    E.preventDefault();
    s(E.target)
  }, !1)
</script>
</body>
</html>