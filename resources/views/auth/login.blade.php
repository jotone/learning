<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ $settings['site_title']->val }}</title>

  @vite('resources/assets/css/reset.scss')
  @vite('resources/assets/css/auth/login.scss')

  {!! $settings['header_code']->value ?? '' !!}
</head>
<body>

<form action="{{ route('auth.login') }}" method="POST" data-form="0">
  @csrf
  <img class="logo-image" src="/images/logo-dashboard.png" alt="">
  <h2>Welcome back</h2>
  <div class="form-body">
    <div class="form-row">
      <input autofocus autocomplete="off" name="email" placeholder="Email" tabindex="1" type="email" required>
      @if($errors->has('email'))
        @foreach($errors->get('email') as $error)
          <p class="error">{!! $error !!}</p>
        @endforeach
      @endif
    </div>

    <div class="form-row">
      <input autocomplete="off" name="password" placeholder="Password" tabindex="2" type="password" required>
      @if($errors->has('password'))
        @foreach($errors->get('password') as $error)
          <p class="error">{!! $error !!}</p>
        @endforeach
      @endif
    </div>

    <div class="form-row link">
      <a href="#" tabindex="4" data-switch="1">Forgot password?</a>
    </div>

    <div class="form-row">
      <button type="submit" tabindex="3"><span>Log in</span></button>
    </div>
  </div>
</form>

<form action="{{ route('reset.send') }}" method="POST" data-form="1">
  @csrf
  <h2>Forgot password?</h2>

  <div class="form-body">
    <div class="form-row">
      <p>Enter the email address you used when you joined and we’ll send you code to reset your password.</p>
    </div>

    <div class="form-row">
      <input autofocus autocomplete="off" name="email" placeholder="Email" tabindex="1" type="email" required>

      @if($errors->has('email'))
        @foreach($errors->get('email') as $error)
          <p class="error">{!! $error !!}</p>
        @endforeach
      @endif
    </div>

    <div class="form-row">
      <button type="submit" tabindex="2"><span>Continue</span></button>
    </div>
  </div>

  <a href="#" class="back-button" data-switch="0" tabindex="3">
    <i class="arrow-icon"></i>
    <span>Back</span>
  </a>
</form>
</div>

{!! $settings['footer_code']->value ?? '' !!}

<script>
  var loc = window.location, nodes = document.querySelectorAll(`a[data-switch]`);
  document.querySelector(`form[data-form="${loc.hash === '#forgot' ? 1 : 0}"]`).classList.add('active')

  for (var i = 0, n = nodes.length; i < n; i++) nodes[i].addEventListener("click", e => {
    e.preventDefault();
    var inner = document.querySelectorAll('form[data-form]')
    var path = loc.origin + loc.pathname
    var dataSwitch = e.target.closest("a").getAttribute('data-switch')
    if (dataSwitch > 0) {
      document.querySelector('body').classList.add('forgot')
      path += '#forgot'
    } else {
      document.querySelector('body').classList.remove('forgot')
    }
    window.history.pushState({}, '', path)
    for (var j = 0, m = inner.length; j < m; j++) {
      inner[j].classList.remove('active');
    }
    document
      .querySelector(`form[data-form="${dataSwitch}"]`)
      .classList.add('active');
  }, false);
</script>
</body>
</html>