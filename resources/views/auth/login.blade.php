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

          <form class="form-switch active" data-trigger="login" action="{{ route('auth.login') }}" method="POST">
            @csrf
            <div class="form-input">
              <label for="email">E-Mail</label>
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
              <label for="password">
                Password
              </label>
              <input
                autocomplete="off"
                id="password"
                name="password"
                required=""
                type="password"
              >
            </div>

            <div class="form-submit">
              <button>Login</button>
            </div>
          </form>

          <form class="form-switch" data-trigger="forgot" action="{{ route('forgot.send') }}" method="POST">
            @csrf

            <div class="form-input">
              <label for="forgot-email">E-Mail</label>
              <input autocomplete="off" id="forgot-email" name="email" class="form-input" type="email" required="" >
            </div>

            <div class="form-submit">
              <button>Submit</button>
            </div>
          </form>

          <div class="form-optional-link">
            <a class="active" href="#" data-target="forgot">Forgot password?</a>
            <a href="#" data-target="login" class="">Cancel</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if(!empty($settings['footer_code']->value ))
    {!! $settings['footer_code']->val !!}
  @endif

<script>{{--var switchForm = function (obj) {
    const target = obj.getAttribute('data-target')
    document.querySelector('form.form-switch.active').classList.remove('active')
    document.querySelector(`form[data-trigger="${target}"]`).classList.add('active')
    document.querySelector('.form-optional-link a.active[data-target]').classList.remove('active')
    document.querySelector(`.form-optional-link a:not([data-target="${target}"])`).classList.add('active')
  }
  var controls = document.querySelectorAll('a[data-target]');
  for (var i = 0, n = controls.length; i < n; i++) {
    controls[i].addEventListener('click',function(e){e.preventDefault();switchForm(e.target)},!1);
  }
  // minimized
  --}}
  let d=document,f="classList",a="active",h="data-target",j="querySelector",k=".form-optional-link",g=t=>{let e=t.getAttribute(h);d[j](`form.form-switch.${a}`)[f].remove(a),d[j](`form[data-trigger="${e}"]`)[f].add(a),d[j](`${k} a.${a}[${h}]`)[f].remove(a),d[j](`${k} a:not([${h}="${e}"])`)[f].add(a)};for(let i=0;i<2;i++)d[j+"All"](`a[${h}]`)[i].addEventListener("click",t=>{t.preventDefault();g(t.target)},!1)
</script>
</body>
</html>