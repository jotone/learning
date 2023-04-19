<template>
  <Layout>
    <template v-slot:optionals>
      <SaveButton form="userCreate"/>
    </template>
    <template v-slot:content>
      <form
        class="page-content-wrap"
        id="userCreate"
        :action="$attrs.routes.users.form"
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

              <InputText caption="Password" name="password" type="password"/>

              <InputText caption="Confirm Password" name="confirmation" type="password"/>

              <TextArea caption="Short bio" name="about" :value="$attrs?.model?.about"/>

              <ImageUpload caption="Profile Image" name="img_url" :dimensions="[200, 200]"/>
            </div>
          </div>

          <div
            class="col-1-2"
            v-if="$attrs.settings.enable_address === '1' || $attrs.settings.enable_phone === '1' || $attrs.settings.enable_shirt_size === '1'"
          >
            <div class="card">
              <div class="card-title">
                User Info
              </div>

              <template v-if="$attrs.settings.enable_address === '1'">
                <div class="form-group">
                  <label class="caption">
                    <span>Timezone</span>
                    <select name="timezone" class="form-select">
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

                <InputText caption="Country" name="country" :value="$attrs?.model?.country"/>

                <InputText caption="State / Region" name="state_region" :value="$attrs?.model?.country"/>

                <InputText caption="City" name="city" :value="$attrs?.model?.city"/>

                <InputText caption="Address" name="address" :value="$attrs?.model?.address"/>

                <InputText caption="Extended address" name="extended_address" :value="$attrs?.model?.extended_address"/>

                <InputText caption="Zip" name="zip" :value="$attrs?.model?.zip"/>
              </template>

              <template v-if="$attrs.settings.enable_phone === '1'">
                <InputText caption="Phone" name="phone" :value="$attrs?.model?.phone"/>
              </template>

              <template v-if="$attrs.settings.enable_shirt_size === '1'">
                <Selector name="shirt_size" caption="T-shirt size" :options="$attrs.enums.shirt_sizes"/>
              </template>
            </div>
          </div>

          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                Miscellaneous
              </div>

              <Selector caption="Role" name="role_id" :options="$attrs.roles"/>

              <Selector
                caption="Status"
                name="status"
                :options="$attrs.enums.statuses"
                v-if="$attrs.hasOwnProperty('model')"
              />
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
import Selector from "../../Shared/Form/Selector.vue";
import TextArea from "../../Shared/Form/TextArea.vue";
import {FormMixin} from "../../Mixins/form-mixin";
import {Timezone} from "../../../libs/timezone";
import "country-select-js/build/js/countrySelect";
import "intl-tel-input/build/js/intlTelInput-jquery.min"

export default {
  components: {ImageUpload, InputText, Selector, TextArea},
  data() {
    return {
      timezones: Timezone
    }
  },
  mixins: [FormMixin],
  name: "Users/Form",
  mounted() {
    $('input[name="country"]').countrySelect({
      defaultCountry: [this.$attrs.model ?? $('html').attr('lang')],
      responsiveDropdown: false
    })

    $('input[name="phone"]').intlTelInput({
      hiddenInput: 'full_number',
      initialCountry: this.$attrs.settings.main_language === 'en' ? 'us' : this.$attrs.settings.main_language,
      preferredCountries: ['de', 'gb', 'us'],
    })
  }
}
</script>

<style>
@import "country-select-js/build/css/countrySelect.min.css";
@import "intl-tel-input/build/css/intlTelInput.min.css";
</style>