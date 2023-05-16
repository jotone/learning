<template>
  <Layout>
    <template v-slot:optionals>
      <SaveButton form="userForm"/>
    </template>

    <template v-slot:content>
      <form
        class="page-content-wrap"
        data-success-callback="resetForm"
        id="userForm"
        :action="$attrs.routes.form"
        method="POST"
        @submit.prevent="submit"
      >
        <input name="_method" type="hidden" value="PUT" v-if="$attrs.hasOwnProperty('model')">
        <div class="row">
          <div class="col-1-2">
            <SimpleUserForm :model="$attrs?.model"/>
          </div>

          <div
            class="col-1-2"
            v-if="$attrs.settings.enable_address === '1' || $attrs.settings.enable_phone === '1' || $attrs.settings.enable_shirt_size === '1'"
          >
            <div class="card">
              <div class="card-title">
                {{ __('user.fields.info') }}
              </div>

              <template v-if="$attrs.settings.enable_address === '1'">
                <div class="form-group">
                  <label class="caption">
                    <span>{{ __('common.timezone') }}</span>
                    <TimezoneSelector name="timezone" :value="$attrs?.model?.timezone"/>
                  </label>
                </div>

                <InputText :caption="__('user.fields.country')" name="country" :value="$attrs?.model?.country"/>

                <InputText :caption="__('user.fields.state')" name="region" :value="$attrs?.model?.region"/>

                <InputText :caption="__('user.fields.city')" name="city" :value="$attrs?.model?.city"/>

                <InputText :caption="__('user.fields.addr')" name="address" :value="$attrs?.model?.address"/>

                <InputText :caption="__('user.fields.addr_ext')" name="ext_addr" :value="$attrs?.model?.extended_address"/>

                <InputText :caption="__('user.fields.zip')" name="zip" :value="$attrs?.model?.zip"/>
              </template>

              <template v-if="$attrs.settings.enable_phone === '1'">
                <InputText :caption="__('user.fields.phone')" name="phone" :value="$attrs?.model?.phone"/>
              </template>

              <template v-if="$attrs.settings.enable_shirt_size === '1'">
                <Selector :caption="__('user.fields.shirt_size')" name="shirt_size" :options="$attrs.enums.shirt_sizes"/>
              </template>
            </div>
          </div>

          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                {{ __('common.misc') }}
              </div>

              <Selector :caption="__('role.single')" name="role_id" :options="$attrs.roles"/>

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
import TimezoneSelector from "../../Shared/Form/TimezoneSelector.vue";
import {FormMixin} from "../../Mixins/form-mixin";
import {Timezone} from "../../../libs/timezone";
import "country-select-js/build/js/countrySelect";
import "intl-tel-input/build/js/intlTelInput-jquery.min"
import SimpleUserForm from "./Partials/SimpleUserForm.vue";

export default {
  components: {SimpleUserForm, ImageUpload, InputText, Selector, TextArea, TimezoneSelector},
  data() {
    return {
      timezones: Timezone
    }
  },
  methods: {
    /**
     * Show one of these message after request
     *
     * @param response
     * @returns {string}
     */
    saveMessage(response) {
      return this.__(`user.msg.${201 === response.status ? 'created' : 'modified'}`, response.data.email)
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