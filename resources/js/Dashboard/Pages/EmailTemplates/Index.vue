<template>
  <Layout>
    <template v-slot:optionals>
      <a class="btn" :href="$attrs.routes.emails.create" v-if="$attrs.auth.role.level === 0">
        <i class="icon plus-icon"></i>
        <span>{{ __('email_templates.create') }}</span>
      </a>

      <SaveButton form="email"/>
    </template>

    <template v-slot:content>
      <div class="page-content-wrap">
        <div class="row">
          <div class="col-1-3">
            <form
              class="card"
              id="smtp"
              method="POST"
              :action="$attrs.routes.settings.update"
              :data-save-message="__('email_templates.smtp.sent')"
              @submit.prevent="submit"
            >
              <input name="_method" type="hidden" value="PATCH">

              <div class="card-title">
                {{ __('email_templates.smtp.settings') }}

                <a class="card-title-link" href="#" data-fancybox data-src="#needHelp">
                  <i class="icon info-icon"></i>
                  <span>{{ __('email_templates.smtp.help') }}</span>
                </a>
              </div>

              <InputText
                name="smtp_username"
                :caption="__('email_templates.smtp.username')"
                :required="true"
                :value="$attrs.content.smtp_username.value"
              />

              <InputText
                name="smtp_password"
                type="password"
                :caption="__('user.password.txt')"
                :required="true"
                :value="$attrs.content.smtp_password.value"
              />

              <InputText
                name="smtp_host"
                :caption="__('email_templates.smtp.host')"
                :required="true"
                :value="$attrs.content.smtp_host.value"
              />

              <InputText
                name="smtp_port"
                :caption="__('email_templates.smtp.port')"
                :required="true"
                :value="$attrs.content.smtp_port.value"
              />

              <InputText
                name="smtp_encryption"
                :caption="__('email_templates.smtp.enc')"
                :required="true"
                :value="$attrs.content.smtp_encryption.value"
              />

              <InputText
                name="smtp_from_address"
                type="email"
                :caption="__('email_templates.smtp.addr')"
                :value="$attrs.content.smtp_from_address.value"
              />

              <InputText
                name="smtp_from_name"
                :caption="__('email_templates.smtp.name')"
                :value="$attrs.content.smtp_from_name.value"
              />

              <div class="form-group">
                <label class="small-caption">
                  <span>
                    *{{ __('email_templates.smtp.msg') }}
                  </span>
                </label>
              </div>

              <div class="form-group">
                <button class="btn blue">
                  {{ __('email_templates.smtp.save') }}
                </button>
              </div>
            </form>
          </div>

          <div class="col-1-3">
            <form
              class="card"
              id="email"
              :data-save-message="__('email_templates.msg.saved')"
              method="POST"
              :action="$attrs.routes.settings.update"
              @submit.prevent="submit"
            >
              <input name="_method" type="hidden" value="PATCH">

              <div class="card-title">{{ __('email_templates.fields.global') }}</div>

              <InputColor
                name="footer_color"
                :caption="__('email_templates.fields.footer_color')"
                :value="$attrs.content.footer_color.value"
              />

              <InputText
                name="terms_of_service"
                :caption="__('email_templates.fields.terms_url')"
                :value="$attrs.content.terms_of_service.value"
              />

              <InputText
                name="privacy_policy"
                :caption="__('email_templates.fields.privacy_url')"
                :value="$attrs.content.privacy_policy.value"
              />

              <InputText
                name="legal_address"
                :caption="__('email_templates.fields.legal_addr')"
                :value="$attrs.content.legal_address.value"
              />
            </form>

            <div class="card">
              <div class="card-title">
                {{ __('email_templates.fields.socials') }}
              </div>

              <div class="form-group">
                <SocialMediaList :list="$attrs.social"/>
              </div>

              <div class="form-group">
                <button class="btn blue" type="button" @click="addSocial">
                  {{ __('common.add') }}
                </button>
              </div>
            </div>
          </div>

          <div class="col-1-3">
            <div class="card">
              <div class="card-title">
                {{ __('email_templates.fields.template_list') }}
              </div>

              <EmailTemplateList :list="$attrs.templates"/>
            </div>
          </div>
        </div>
      </div>
    </template>

    <template v-slot:popup>
      <div class="modal" id="needHelp" style="max-width: 700px; display: none">
        <div class="modal-title">
          {{ __('email_templates.smtp.help_title') }}
        </div>
        <div class="modal-body">
          <div class="section">
            <div class="section-title">
              <span>
                {{ __('email_templates.smtp.help_msg1') }}</span>
            </div>
            <div class="section-body">
              <iframe
                src="https://www.loom.com/embed/23efa28885b34edfba8c11ee88aab24b"
                frameborder="0"
                webkitallowfullscreen
                mozallowfullscreen
                allowfullscreen
              ></iframe>
            </div>
          </div>
          <div class="section">
            <div class="section-title">
              <span>
                {{ __('email_templates.smtp.help_msg2') }}
              </span>
            </div>
            <div class="section-body">
              <iframe
                src="https://www.loom.com/embed/c68f255b07e14218887fe4593173a2df"
                frameborder="0"
                webkitallowfullscreen
                mozallowfullscreen
                allowfullscreen
              ></iframe>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Layout>
</template>

<script>

import {Fancybox} from "@fancyapps/ui";
import {FormMixin} from "../../Mixins/form-mixin";
import InputText from "../../Shared/Form/InputText.vue";
import InputColor from "../../Shared/Form/InputColor.vue";
import SocialMediaList from "./Partials/SocialMediaList.vue";
import EmailTemplateList from "./Partials/EmailTemplateList.vue";

export default {
  components: {EmailTemplateList, InputColor, InputText, SocialMediaList},
  computed: {
    /**
     * Default Select2 Options
     * @returns {{allowHtml: boolean, minimumResultsForSearch: number, templateSelection: function, templateResult: function}}
     */
    select2Options() {
      return {
        allowHtml: true,
        minimumResultsForSearch: -1,
        templateResult: this.formatOption,
        templateSelection: this.formatOption
      }
    }
  },
  data() {
    return {
      allowRequest: true,
      socialList: this.$attrs.social
    }
  },
  mixins: [FormMixin],
  name: "EmailTemplates/Index",
  methods: {
    /**
     * Add social media entity
     */
    addSocial() {
      if (this.allowRequest) {
        this.allowRequest = !1;
        // Social media items number
        const itemsCount = $('.drag-list li').length
        // Prepare form data
        let formData = new FormData();
        formData.append('type', 'facebook')
        formData.append('url', '')
        formData.append('position', itemsCount)

        // Send request to store social media link
        this.request({
          method: 'post',
          url: this.$attrs.routes.social.store,
          data: formData,
          hidePreloader: !0,
          preventNotification: !0,
          onSuccess: response => {
            this.allowRequest = !0;
            if (201 === response.status) {
              // Add item to list
              this.$attrs.social.push({
                id: response.data.id,
                type: response.data.type,
                url: response.data.url,
              });

              // Init select2
              const itemAddedInterval = setInterval(() => {
                if ($('.drag-list li').length > itemsCount) {
                  $('.drag-list li:last select[name="social_type"]').select2(this.select2Options)
                  clearInterval(itemAddedInterval)
                }
              }, 10)
            }
          }
        })
      }
    },
    /**
     * Select2 option html template
     * @param opt
     * @returns {*|jQuery|HTMLElement}
     */
    formatOption(opt) {
      return $(`<span class="option-small"><i class="icon social-${opt.id}"></i>${opt.text}</span>`);
    }
  },
  mounted() {
    Fancybox.bind('a[data-fancybox]', {
      hideScrollbar: false
    });

    $('select[name="social_type"]').select2(this.select2Options)
  }
}
</script>
