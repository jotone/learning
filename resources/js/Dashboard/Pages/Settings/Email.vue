<template>
  <Layout>
    <template v-slot:optionals>
      <SaveButton form="email"/>
    </template>

    <template v-slot:content>
      <div class="page-content-wrap">
        <div class="row">
          <div class="col-1-3">
            <form
              class="card"
              id="smtp"
              data-save-message="Mail for SMTP testing was successfully sent."
              method="POST"
              :action="$attrs.routes.settings.update"
              @submit.prevent="submit"
            >
              <Method value="PATCH"/>

              <div class="card-title">
                SMTP Settings

                <a class="card-title-link" href="#" data-fancybox data-src="#needHelp">
                  <i class="icon info-icon"></i>
                  <span>Need Help?</span>
                </a>
              </div>

              <InputText
                caption="Username"
                name="smtp_username"
                :required="true"
                :value="$attrs.content.smtp_username.value"
              />

              <InputText
                caption="Password"
                name="smtp_password"
                type="password"
                :required="true"
                :value="$attrs.content.smtp_password.value"
              />

              <InputText
                caption="Host"
                name="smtp_host"
                :required="true"
                :value="$attrs.content.smtp_host.value"
              />

              <InputText
                caption="Port"
                name="smtp_port"
                :required="true"
                :value="$attrs.content.smtp_port.value"
              />

              <InputText
                caption="Encryption"
                name="smtp_encryption"
                :required="true"
                :value="$attrs.content.smtp_encryption.value"
              />

              <InputText
                caption="From Address"
                name="smtp_from_address"
                type="email"
                :value="$attrs.content.smtp_from_address.value"
              />

              <InputText caption="From name" name="smtp_from_name" :value="$attrs.content.smtp_from_name.value"/>

              <div class="form-group">
                <label class="small-caption">
                  <span>
                    *If you are using GSuite make sure to enable less secure apps from your GSuite administration and
                    also on the account level.
                  </span>
                </label>
              </div>

              <div class="form-group">
                <button class="btn blue">Save and send test email</button>
              </div>
            </form>
          </div>

          <div class="col-1-3">
            <form
              class="card"
              id="email"
              data-save-message="Email Settings were successfully saved."
              method="POST"
              :action="$attrs.routes.settings.update"
              @submit.prevent="submit"
            >
              <Method value="PATCH"/>

              <div class="card-title">Email Global Settings</div>

              <InputColor
                caption="Footer Color"
                name="footer_color"
                :value="$attrs.content.footer_color.value"
              />

              <InputText
                caption="Terms Of Service Link"
                name="terms_of_service"
                :value="$attrs.content.terms_of_service.value"
              />

              <InputText
                caption="Privacy Policy Link"
                name="privacy_policy"
                :value="$attrs.content.privacy_policy.value"
              />

              <InputText
                caption="Legal Address"
                name="legal_address"
                :value="$attrs.content.legal_address.value"
              />
            </form>

            <div class="card">
              <div class="card-title">
                Social Media Links on Footer
              </div>

              <div class="form-group">
                <ul class="drag-list">
                  <template v-for="social in $attrs.social">
                    <SocialMediaListItem :item="social"/>
                  </template>
                </ul>
              </div>

              <div class="form-group">
                <button class="btn blue" type="button" @click="addSocial">Add</button>
              </div>
            </div>
          </div>

          <div class="col-1-3">
            <div class="card">
              <div class="card-title">
                Templates List
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <template v-slot:popup>
      <div class="modal" id="needHelp" style="max-width: 700px; display: none">
        <div class="modal-title">
          How to setup your SMTP settings
        </div>
        <div class="modal-body">
          <div class="section">
            <div class="section-title">
              <span>Please watch the following video in order to be able to setup your emails in the platform:</span>
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
                Using Sendgrid as your SMTP can be the best option to ensure deliverability.
                It is free for up to 100 emails per day.
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
import Method from "../../Shared/Form/Method.vue";
import InputColor from "../../Shared/Form/InputColor.vue";
import SocialMediaListItem from "./Partials/SocialMediaListItem.vue";
import 'select2/dist/js/select2.min'

export default {
  components: {SocialMediaListItem, InputColor, InputText, Method},
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
      allowRequest: true
    }
  },
  mixins: [FormMixin],
  name: "Settings/Email",
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

<style>
@import "/node_modules/@fancyapps/ui/dist/fancybox/fancybox.css";
@import "/node_modules/select2/dist/css/select2.min.css";
</style>
