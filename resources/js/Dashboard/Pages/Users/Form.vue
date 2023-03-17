<template>
  <Layout>
    <template v-slot:optionals>
      <button type="submit" form="userCreate" class="btn">
        <i class="icon save-icon"></i>
      </button>
    </template>
    <template v-slot:content>
      <form
        class="page-content-wrap"
        id="userCreate"
        :action="$attrs.routes.users[$attrs.hasOwnProperty('model') ? 'update' : 'store']"
        method="POST"
        @submit.prevent="submit"
      >
        <div class="row">
          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                Profile Data
              </div>
              <InputText caption="First Name" name="first_name" :value="$attrs?.model?.first_name"/>

              <InputText caption="Last Name" name="last_name" :value="$attrs?.model?.last_name"/>

              <InputText caption="E-mail" name="email" :value="$attrs?.model?.email"/>

              <TextArea caption="Short bio" name="about" :value="$attrs?.model?.about"/>

              <ImageUpload caption="Profile Image" name="img_url" :dimensions="[200, 200]"/>
            </div>
          </div>

          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                User Info
              </div>

              <InputText caption="Country" name="country" :value="$attrs?.model?.country"/>

              <InputText caption="State / Region" name="state_region" :value="$attrs?.model?.country"/>

              <div class="form-group">
                <label class="caption">
                  <span>Timezone</span>
                  <select name="site_timezone" class="form-select">
                    <template v-for="(timezoneList, key) in timezones">
                      <optgroup :label="key">
                        <option
                          v-for="timezone in timezoneList"
                          :selected="$attrs.hasOwnProperty('model') && $attrs.model.timezone === key"
                          :value="key"
                        >
                          {{ timezone }}
                        </option>
                      </optgroup>
                    </template>
                  </select>
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                Miscellaneous
              </div>

              <div class="form-group">
                <label class="caption">
                  <span>Role</span>
                  <select name="role_id" class="form-select">
                    <option
                      v-for="(roleName, roleID) in $attrs.roles"
                      :selected="$attrs.hasOwnProperty('model') && $attrs.model.role_id === roleID"
                      :value="roleID"
                    >
                      {{ roleName }}
                    </option>
                  </select>
                </label>
              </div>
            </div>
          </div>
        </div>
      </form>
    </template>
  </Layout>
</template>

<script>

import ImageUpload from "../../Shared/Form/ImageUpload.vue";
import InputText from "../../Shared/Form/InputText.vue";
import Layout from "../../Shared/Layout.vue";
import TextArea from "../../Shared/Form/TextArea.vue";
import {FormMixin} from "../../Mixins/form-mixin";
import {Timezone} from "../../../libs/timezone";
import 'country-select-js/build/js/countrySelect'

export default {
  components: {ImageUpload, InputText, Layout, TextArea},
  data() {
    return {
      timezones: Timezone
    }
  },
  name: "Users/Form",
  mixins: [FormMixin],
  mounted() {
    $('input[name="country"]').countrySelect({
      defaultCountry: [this.$attrs.model ?? $('html').attr('lang')],
      responsiveDropdown: false
    })
  }
}
</script>

<style>
  @import "/node_modules/country-select-js/build/css/countrySelect.min.css";
</style>