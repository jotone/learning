<template>
  <Layout>
    <template v-slot:optionals>
      <SaveButton form="loginPage"/>
    </template>

    <template v-slot:content>
      <form
        class="page-content-wrap"
        id="loginPage"
        data-save-message="Login page settings were successfully saved."
        method="POST"
        :action="$attrs.routes.settings.update"
        @submit.prevent="submit"
      >
        <input name="_method" type="hidden" value="PATCH">
        <div class="row">
          <div class="col-1-3">
            <div class="card">
              <div class="card-title">
                Login Page Main Colors
              </div>

              <InputColor
                caption="Login page background color"
                name="login_bg_color"
                :value="$attrs.content.login_bg_color.value"
                @change="preview.bg.color = $event.target.value"
              />

              <InputColor
                caption="Logo image background color"
                name="login_logo_bg_color"
                :value="$attrs.content.login_logo_bg_color.value"
                @change="preview.logo.color = $event.target.value"
              />

              <InputColor
                caption="Form background color"
                name="login_form_bg_color"
                :value="$attrs.content.login_form_bg_color.value"
                @change="preview.form.color = $event.target.value"
              />

              <InputColor
                caption="Form text color"
                name="login_form_text_color"
                :value="$attrs.content.login_form_text_color.value"
                @change="preview.form.text = $event.target.value"
              />

              <div class="form-group">
                <label class="caption">
                  <span>Login form buttons</span>
                </label>

                <ButtonSettings
                  caption="Button"
                  name="login_btn"
                  :value="JSON.parse($attrs.content.login_btn.value)"
                />
              </div>
            </div>

            <div class="card">
              <div class="card-title">
                Login Background Image
              </div>

              <ImageUpload
                name="login_bg_img"
                :change="previewImage"
                :dimensions="[1280, 1024]"
                :formats="['png', 'jpg']"
              />
            </div>
          </div>
          <div class="col-2-3">
            <div class="card">
              <div class="card-title">
                Preview
              </div>

              <div
                class="login-page-preview"
                :style="{'background-color': preview.bg.color, 'background-image': !!preview.bg.img ? `url(${preview.bg.img})` : null }">
                <div class="login-form-wrap">
                  <div class="logo-wrap" :style="{'background-color': preview.logo.color}">
                    <img :src="preview.logo.img" alt="">
                  </div>
                  <div class="form-wrap" :style="{'background-color': preview.form.color}">
                    <div class="form-switch">
                      <div class="form-input">
                        <label :style="{color: preview.form.text}">E-Mail</label>
                        <input disabled="disabled" placeholder="example@email.com" type="email" :style="{color: preview.form.text}">
                      </div>
                      <div class="form-input">
                        <label :style="{color: preview.form.text}">Password</label>
                        <input disabled="disabled" type="password" :style="{color: preview.form.text}">
                      </div>
                      <div class="form-submit">
                        <button :style="{
                          'background-color': preview.btn.normal['background-color'],
                          'border-color': preview.btn.normal['border-color'],
                          'color': preview.btn.normal.color
                        }" @mouseenter="hoverBtnEnter" @mouseleave="hoverBtnLeave">
                          Login
                        </button>
                      </div>
                    </div>

                    <div class="form-optional-link">
                      <a href="javascript:void(0)">Forgot password?</a>
                    </div>
                  </div>
                </div>
              </div>
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
import InputColor from "../../Shared/Form/InputColor.vue";
import {FormMixin} from "../../Mixins/form-mixin";

export default {
  components: {ButtonSettings, ImageUpload, InputColor},
  data() {
    return {
      preview: {
        bg: {
          color: this.$attrs.content.login_bg_color.value,
          img: this.$attrs.content.login_bg_img.value,
        },
        btn: JSON.parse(this.$attrs.content.login_btn.value),
        form: {
          color: this.$attrs.content.login_form_bg_color.value,
          text: this.$attrs.content.login_form_text_color.value
        },
        logo: {
          color: this.$attrs.content.login_logo_bg_color.value,
          img: this.$attrs.content.logo_img.value
        }
      }
    }
  },
  methods: {
    /**
     * Set uploaded image to preview
     * @param reader
     */
    previewImage(reader) {
      this.preview.bg.img = reader.result
    },
    /**
     * Button hover: mouse in
     * @param e
     */
    hoverBtnEnter(e) {
      this.setCss(e, this.preview.btn.hover)
    },
    /**
     * Button hover: mouse out
     * @param e
     */
    hoverBtnLeave(e) {
      this.setCss(e, this.preview.btn.normal)
    },
    /**
     * Set css styles to the preview login button
     * @param e
     * @param css
     */
    setCss(e, css) {
      const obj = $(e.target).closest('button')
      obj.css({
        'background-color': css['background-color'],
        'border-color': css['border-color'],
        'color': css.color
      })
    }
  },
  mixins: [FormMixin],
  mounted() {
    // Preview Login form buttons
    $('.buttons-settings-wrap .jscolor').on('change', e => {
      const _this = $(e.target)
      const path = _this.attr('name').replace(/login_btn\[|\]/g, '').split('[')
      this.preview.btn[path[0]][path[1]] = _this.val()
    })
  },
  name: "Settings/Login"
}
</script>