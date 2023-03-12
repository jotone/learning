* {
  box-sizing: border-box;
  font-family: Rubik, sans-serif;
  margin: 0;
  padding: 0
}

.login-page {
  background-color: {{ $settings['login_bg_color']->val }};
  @if(!empty($settings['login_bg_img']->value))
  background-image: url("{{ $settings['login_bg_img']->val }}");
  @endif
  background-position: 50%;
  background-repeat: no-repeat;
  background-size: cover;
  display: flex;
  flex-direction: column;
  min-height: 100vh
}

.content-clear {
  margin: auto;
  padding: 40px;
  width: 100%
}

.login-form {
  background: {{ $settings['login_form_bg_color']->val }};
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(29, 33, 91, .2);
  margin: auto;
  max-width: 410px
}

.login-form .logo-wrap {
  align-items: center;
  background-color: {{ $settings['login_logo_bg_color']->val }};
  border-radius: 6px 6px 0 0;
  color: {{ $settings['login_form_text_color']->val }};
  display: flex;
  height: 286px;
  justify-content: center;
  overflow: hidden;
  padding: 26px
}

.login-form .logo-wrap picture {
  display: flex;
  height: 100%;
  justify-content: center;
  max-height: 164px;
  max-width: 378px;
  width: 100%
}

.login-form .logo-wrap img {object-fit: contain; max-width: 100%}

.login-form .form {background-color: {{ $settings['login_form_bg_color']->val }}; padding: 10px 26px 26px 26px}

.form-switch {display: none}

.form-switch.active {display: block}

.form-input, .form-submit {margin-bottom: 20px}

.form-input label {
  color: {{ $settings['login_form_text_color']->val }};
  display: inline-block;
  font-size: 14px;
  line-height: 16px
}

.form-input input {
  border: 1px solid #d6e0ff;
  border-radius: 4px;
  color: #00145e;
  font-family: Rubik, sans-serif;
  font-size: 14px;
  height: 40px;
  line-height: 18px;
  margin-top: 6px;
  padding: 0 15px;
  width: 100%
}

.form-submit button {
  background-color: {{ $settings['login_btn']->val->normal->{'background-color'} }};
  border: 2px solid {{ $settings['login_btn']->val->normal->{'border-color'} }};
  border-radius: 6px;
  color: {{ $settings['login_btn']->val->normal->color }};
  cursor: pointer;
  font: 500 14px/16px Rubik, sans-serif;
  padding: 14px 30px;
  text-align: center;
  width: 100%
}

.form-submit button:hover {
  background-color: {{ $settings['login_btn']->val->hover->{'background-color'} }};
  border-color: {{ $settings['login_btn']->val->hover->{'border-color'} }};
  color: {{ $settings['login_btn']->val->hover->color }}
}

.form-optional-link {
  font-size: 14px;
  line-height: 18px;
  margin: 0;
  text-align: center
}

.form-optional-link a {
  color: #909bbf;
  display: none;
  text-decoration: none
}

.form-optional-link a.active {display:block}

.form-errors {list-style: none}

.form-errors li {
  color: #d6e0ff;
  font-size: 14px;
  margin: 10px 0
}