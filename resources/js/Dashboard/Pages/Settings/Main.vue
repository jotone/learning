<template>
  <DefaultLayout :menu="$attrs.menu" :routes="$attrs.routes">
    <TopMenu :menu="$attrs.top_menu"/>

    <form class="page-content-wrap cut-form">
      <div class="row">
        <div class="col-1-2">
          <div class="card">
            <div class="card-title">
              Site Info
            </div>
            <InputText caption="Site URL" disabled="" name="site_url" :value="$attrs.content.site_url.value"/>

            <InputText caption="System Name" name="site_title" :value="$attrs.content.site_title.value"/>

            <ImageUpload
              caption="Fav Icon (SVG Icon, or PNG 192×192 px)"
              name="fav_icon"
              :dimensions="[192, 192]"
              :formats="['png', 'svg']"
            />

            <ImageUpload
              caption="Logo Image"
              name="logo_img_url"
            />
          </div>

          <div class="card">
            <div class="card-title">
              Button Settings
            </div>
            <div class="form-group">
              <label class="caption">
                <span>Primary buttons</span>
              </label>

              <ButtonSettings name="primary_btn" :value="JSON.parse($attrs.content.primary_btn.value)"/>
            </div>
          </div>
        </div>
      </div>
    </form>
  </DefaultLayout>
</template>

<script>
import DefaultLayout from "../../Layouts/DefaultLayout.vue";
import ImageUpload from "../../Layouts/Form/ImageUpload.vue";
import InputText from "../../Layouts/Form/InputText.vue";
import TopMenu from "../../Layouts/TopMenu.vue";
import ButtonSettings from "../../Layouts/Form/ButtonSettings.vue";

export default {
  components: {ButtonSettings, DefaultLayout, ImageUpload, InputText, TopMenu},
  name: "Settings/Main"
}
</script>