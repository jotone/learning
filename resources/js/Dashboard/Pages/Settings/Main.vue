<template>
  <DefaultLayout :menu="$attrs.menu" :routes="$attrs.routes">
    <template v-slot:optionals>
      <button type="submit" form="mainSettings" class="btn">
        <i class="icon save-icon"></i>
      </button>
    </template>

    <template v-slot:content>
      <TopMenu :menu="$attrs.top_menu"/>

      <form
        class="page-content-wrap cut-form"
        id="mainSettings"
        :action="$attrs.routes.settings.update"
        method="POST"
        @submit.prevent="submit"
      >
        <input name="_method" type="hidden" value="PATCH">
        <div class="row">
          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                Site Info
              </div>
              <InputText caption="Site URL" disabled="" name="site_url" :value="$attrs.content.site_url.value"/>

              <InputText
                caption="System Name"
                name="site_title"
                :value="$attrs.content.site_title.value"
              />

              <ImageUpload
                caption="Fav Icon (SVG Icon, ICO file, or PNG 192×192 px)"
                name="fav_icon"
                :dimensions="[192, 192]"
                :formats="['png', 'svg', 'ico']"
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

                <ButtonSettings
                  caption="Button"
                  name="primary_btn"
                  :value="JSON.parse($attrs.content.primary_btn.value)"
                />
              </div>
            </div>
          </div>

          <div class="col-1-2">
            <div class="card">
              <div class="form-group-wide">
                <label class="card-title">
                  <input
                    class="form-checkbox"
                    type="checkbox"
                    name="custom-domain"
                    :checked="$attrs.content.site_custom_url.value.length"
                    @change="enableCustomDomain"
                  >
                  Enable Custom Domain
                </label>
              </div>

              <InputText
                caption="Custom URL"
                name="site_custom_url"
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
                  Go to your domain DNS provider and create a CNAME record with name <span><a href="#"></a></span>
                  or * and target <a href="https://copemember.mycopemember.com">https://copemember.mycopemember.com</a>.
                </p>
                <p>
                  When you complete changing your DNS please click Submit. Our team will finish the process on our side
                  within 24 working hours.
                </p>
              </div>

              <div class="form-group" :style="`display: ${customDomainSet ? 'block' : 'none'}`">
                <button type="submit" class="btn regular">Submit</button>
              </div>
            </div>

            <div class="card">
              <div class="card-title">
                Menu Colors
              </div>

              <ButtonSettings
                caption="Normal"
                name="menu_colors"
                :value="JSON.parse($attrs.content.menu_colors.value)"
              />
            </div>

            <div class="card">
              <div class="card-title">
                Timezone
              </div>
              <div class="form-group">
                <select name="site_timezone" class="form-select">
                  <option
                    v-for="(timezone, key) in timezones"
                    :selected="key === $attrs.content.site_timezone.value"
                    :value="key"
                  >
                    {{ key }}
                  </option>
                </select>
              </div>
            </div>

            <div class="card">
              <div class="card-title">
                Custom Scripts
              </div>

              <TextArea
                caption="Header code"
                name="header_code"
                placeholder="Enter header code scripts code here..."
                :value="$attrs.content.header_code.value"
              />

              <TextArea
                caption="Footer code"
                name="footer_code"
                placeholder="Enter footer code scripts code here..."
                :value="$attrs.content.footer_code.value"
              />
            </div>

            <div class="card">
              <div class="card-title">
                Custom Theme Styles
              </div>

              <TextArea
                name="override_css"
                placeholder="Insert code here..."
              />
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
import InputText from "../../Layouts/Form/InputText.vue";
import TextArea from "../../Layouts/Form/TextArea.vue";
import TopMenu from "../../Layouts/TopMenu.vue";
import {showNotification} from "../../../libs/notifications";
import {Timezone} from "../../Mixins/timezone";

export default {
  components: {TextArea, ButtonSettings, DefaultLayout, ImageUpload, InputText, TopMenu},
  name: "Settings/Main",
  data() {
    return {
      customDomainEnabled: this.$attrs.content.site_custom_url.value.length > 0,
      customDomainSet: this.$attrs.content.site_custom_url.value.length > 0,
      messages: {
        saved: 'Settings were successfully saved.'
      }
    }
  },
  mixins: [Timezone],
  methods: {
    enableCustomDomain(e) {
      this.customDomainEnabled = $(e.target).closest('input[type="checkbox"]').prop('checked')
    },
    setCustomDomain(e) {
      const value = $(e.target).closest('input').val().trim()
      this.customDomainSet = value.length > 0 && value.indexOf('.') > 0
    },
    submit(e) {
      const form = $(e.target).closest('form');

      if (typeof form.attr('action') === 'undefined') {
        throw new ReferenceError('Form action attribute is not declared.')
      }

      let formData = new FormData(form[0])

      if (typeof form.attr('id') !== 'undefined') {
        const formID = form.attr('id')
        $('#app').find(`[form="${formID}"]`).each(function () {
          const tag = $(this).prop('tagName').toLowerCase()
          const name = $(this).attr('name')
          if (typeof name !== 'undefined') {
            switch (tag) {
              case 'input':
                const type = $(this).attr('type')
                if (typeof type === 'undefined') {
                  formData.append(name, $(this).val())
                } else {
                  switch (type.toLowerCase()) {
                    case 'checkbox':
                      formData.append(name, $(this).prop('checked'))
                      break;
                    case 'radio':
                      formData.append(name, $(`${tag}[name="${name}"]:checked`).val())
                      break;
                    case 'hidden':
                    case 'number':
                    case 'text':
                      formData.append(name, $(this).val())
                      break;
                    case 'file':
                      formData.append(name, $(this).prop('files'))
                      break;
                  }
                }
                break;
              case 'select':
              case 'textarea':
                formData.append(name, $(this).val())
                break;
              default:
                console.log(name, $(this).val())
            }
          }
        })
      }

      const method = typeof form.attr('method') === 'undefined' ? 'get' : form.attr('method').toLowerCase();

      $.axios.interceptors.request.use((config) => {
        $('.preloader').show()
        return config;
      });

      $.axios[method](form.attr('action'), formData, {
        headers: {
          "content-type": "multipart/form-data",
          "accept": "application/json"
        }
      })
        .then(response => {
          $('.preloader').hide()

          showNotification({
            type: 'success',
            text: [this.messages.saved]
          })
        })
        .catch(error => {
          $('.preloader').hide()

          let messages = []
          if (error.hasOwnProperty('response')) {
            messages.push({
              type: 'error',
              caption: error.response.statusText,
              text: Object.keys(error.response.data.errors).map(key => error.response.data.errors[key]).flat(2)
            })
          } else if (error.hasOwnProperty('request')) {
            let errors = JSON.parse(error.request.responseText)
            messages.push({
              type: 'error',
              caption: error.request.statusText,
              text: Object.keys(errors).map(key => errors[key]).flat(2)
            })
          } else if (error.hasOwnProperty('message')) {
            messages.push({
              type: 'error',
              caption: error.name,
              text: [error.message]
            })
          } else {
            console.error(error)
          }

          showNotification(messages)
        })
    }
  }
}
</script>