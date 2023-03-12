<template>
  <DefaultLayout>
    <template v-slot:optionals>
      <button type="submit" form="loginPage" class="btn">
        <i class="icon save-icon"></i>
      </button>
    </template>

    <template v-slot:content>
      <TopMenu/>

      <form
        class="page-content-wrap"
        id="loginPage"
        :action="$attrs.routes.settings.update"
        method="POST"
        @submit.prevent="submitForm"
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
              />

              <InputColor
                caption="Logo image background color"
                name="login_logo_bg_color"
                :value="$attrs.content.login_logo_bg_color.value"
              />

              <InputColor
                caption="Form background color"
                name="login_form_bg_color"
                :value="$attrs.content.login_form_bg_color.value"
              />

              <InputColor
                caption="Form text color"
                name="login_form_text_color"
                :value="$attrs.content.login_form_text_color.value"
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
            </div>
          </div>
        </div>
      </form>

    </template>
  </DefaultLayout>
</template>

<script>

import ButtonSettings from "../../Layouts/Form/ButtonSettings.vue";
import DefaultLayout from "../../Layouts/DefaultLayout.vue";
import ImageUpload from "../../Layouts/Form/ImageUpload.vue";
import InputColor from "../../Layouts/Form/InputColor.vue";
import TopMenu from "../../Layouts/TopMenu.vue"
import {FormMixin} from "../../Mixins/form-mixin";

export default {
  components: {ButtonSettings,DefaultLayout, ImageUpload, InputColor, TopMenu},
  name: "Settings/Login",
  data() {
    return {
      messages: {
        saved: 'Login page settings were successfully saved.'
      }
    }
  },
  mixins: [FormMixin]
}
</script>