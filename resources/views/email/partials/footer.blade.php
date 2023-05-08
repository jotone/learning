<div class="footer-wrap" style="background-color: {{ $settings['footer_color']['value'] ?? '#000000' }}">
  @include('email.partials.company-logo', ['logo' => $settings['logo_img']])

  @includeWhen(!empty($social_links), 'email.partials.social-links')

  {{--<div class="extra-links">
    @if (isset($settings['email-settings-global']['terms_of_service_link']) &&
            strlen($settings['email-settings-global']['terms_of_service_link']['value']) > 0)
      <a href="{{ $settings['email-settings-global']['terms_of_service_link']['value'] }}">@lang('adminSettingsEmail.termsOfService')</a>
    @endif
    •
    @if (isset($settings['email-settings-global']['privacy_policy_link']) &&
            strlen($settings['email-settings-global']['privacy_policy_link']['value']) > 0)
      <a href="{{ $settings['email-settings-global']['privacy_policy_link']['value'] }}">@lang('adminSettingsEmail.privacyPolicy')</a>
    @endif
  </div>--}}


  @if(!empty($settings['legal_address']['value']))
    <div class="address">
      <span>{{ $settings['address']['value'] }}</span>
    </div>
  @endif
  <div class="copyright-wrap">&nbsp;&copy;&nbsp;</div>
</div>