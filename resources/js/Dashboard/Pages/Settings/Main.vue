<template>
  <Layout>
    <template v-slot:optionals>
      <SaveButton form="mainSettings"/>
    </template>

    <template v-slot:content>
      <form
          class="page-content-wrap cut-form"
          id="mainSettings"
          :action="$attrs.routes.form"
          method="POST"
          @submit.prevent="submit"
      >
        <input name="_method" type="hidden" value="PATCH">
        <div class="row">
          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                {{ __('settings.main.info') }}
              </div>
              <InputText
                  name="site_url"
                  :caption="__('settings.main.site_url')"
                  :disabled="true"
                  :value="$attrs.content.site_url.value"
              />

              <InputText
                  name="site_title"
                  :caption="__('settings.main.title')"
                  :value="$attrs.content.site_title.value"
              />

              <ImageUpload
                  caption="Fav Icon (SVG Icon, ICO file, or PNG 192×192 px)"
                  name="fav_icon"
                  :dimensions="[192, 192]"
                  :formats="['png', 'svg', 'ico']"
              />

              <ImageUpload caption="Logo Image" name="logo_img"/>
            </div>

            <div class="card">
              <div class="card-title">
                {{ __('settings.buttons.caption') }}
              </div>
              <div class="form-group">
                <label class="caption">
                  <span>{{ __('settings.buttons.primary') }}</span>
                </label>

                <ButtonSettings
                    name="primary_btn"
                    :caption="__('common.button')"
                    :value="JSON.parse($attrs.content.primary_btn.value)"
                />
              </div>
            </div>
          </div>

          <div class="col-1-2">
            <div class="card">
              <div class="form-group-wide">
                <label class="card-title-left">
                  <input
                      class="form-checkbox"
                      type="checkbox"
                      name="custom-domain"
                      :checked="$attrs.content.site_custom_url.value.length"
                      @change="enableCustomDomain"
                  >
                  {{ __('settings.custom_domain.enable') }}
                </label>
              </div>

              <InputText
                  name="site_custom_url"
                  :caption="__('settings.main.custom_url')"
                  :style="`display: ${customDomainEnabled ? 'block' : 'none'}`"
                  :value="$attrs.content.site_custom_url.value"
                  @paste="setCustomDomain"
                  @keyup="setCustomDomain"
              />

              <div
                  class="card-text"
                  :style="`display: ${customDomainSet ? 'block' : 'none'}`"
              >
                <p>
                  {{ __('settings.custom_domain.msg1pt1') }}
                  <span><a href="javascript:void(0)"></a></span>
                  {{ __('settings.custom_domain.msg1pt2') }}
                  <a href="https://copemember.mycopemember.com">https://copemember.mycopemember.com</a>.
                </p>
                <p>
                  {{ __('settings.custom_domain.msg2') }}
                </p>
              </div>

              <div class="form-group" :style="`display: ${customDomainSet ? 'block' : 'none'}`">
                <button type="submit" class="btn regular">{{ __('common.submit') }}</button>
              </div>
            </div>

            <div class="card">
              <div class="card-title">
                {{ __('settings.main.menu_colors') }}
              </div>

              <ButtonSettings
                  name="menu_colors"
                  :caption="__('settings.buttons.normal')"
                  :value="JSON.parse($attrs.content.menu_colors.value)"
              />
            </div>

            <div class="card">
              <div class="card-title">
                {{ __('common.timezone') }}
              </div>
              <TimezoneSelector name="site_timezone" :value="$attrs.content.site_timezone.value"/>
            </div>

            <div class="card">
              <div class="card-title">
                {{ __('settings.main.custom_script') }}
              </div>

              <TextArea
                  name="header_code"
                  :caption="__('settings.main.header_code')"
                  :placeholder="__('settings.main.header_placeholder')"
                  :value="$attrs.content.header_code.value"
              />

              <TextArea
                  name="footer_code"
                  :caption="__('settings.main.footer_code')"
                  :placeholder="__('settings.main.footer_placeholder')"
                  :value="$attrs.content.footer_code.value"
              />
            </div>

            <div class="card">
              <div class="card-title">
                {{ __('settings.main.theme_code') }}
              </div>

              <TextArea
                  name="override_css"
                  :placeholder="__('settings.main.theme_placeholder')"
                  :value="$attrs.overrideCss"
              />
            </div>
          </div>
        </div>
      </form>
    </template>
  </Layout>
</template>

<script>

import ButtonSettings from "../../Shared/Form/ButtonSettings.vue";
import ImageUpload from "../../Shared/Form/ImageUpload.vue";
import InputText from "../../Shared/Form/InputText.vue";
import TextArea from "../../Shared/Form/TextArea.vue";
import TimezoneSelector from "../../Shared/Form/TimezoneSelector.vue";
import {FormMixin} from "../../Mixins/form-mixin";

export default {
  components: {ButtonSettings, ImageUpload, InputText, TextArea, TimezoneSelector},
  data() {
    return {
      customDomainEnabled: this.$attrs.content.site_custom_url.value.length > 0,
      customDomainSet: this.$attrs.content.site_custom_url.value.length > 0,
    }
  },
  methods: {
    enableCustomDomain(e) {
      this.customDomainEnabled = $(e.target).closest('input[type="checkbox"]').prop('checked')
    },
    saveMessage() {
      return this.__('settings.main.success')
    },
    setCustomDomain(e) {
      const value = $(e.target).closest('input').val().trim()
      this.customDomainSet = value.length > 0 && value.indexOf('.') > 0
    }
  },
  mixins: [FormMixin],
  name: "Settings/Main"
}
</script>