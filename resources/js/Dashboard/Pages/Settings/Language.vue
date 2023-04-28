<template>
  <Layout>
    <template v-slot:content>
      <div class="page-content-wrap">
        <div class="row">
          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                Select Main Language
              </div>

              <form
                id="selectLang"
                :action="$attrs.routes.settings.update"
                data-save-message="Main language was successfully set."
                method="POST"
                @submit.prevent="submit"
              >
                <Method value="PATCH"/>
                <div class="form-group">
                  <label class="caption">
                    <select name="main_language" class="form-select">
                      <option
                        v-for="lang in installed"
                        :selected="lang === $attrs.settings.main_language"
                        :value="lang"
                      >
                        {{ $page.props.available[lang] }}
                      </option>
                    </select>
                  </label>
                </div>

                <div class="form-group shift-right">
                  <button name="apply" class="btn blue">
                    Save
                  </button>
                  <button
                    name="remove"
                    class="btn"
                    type="button"
                    :data-url="$attrs.routes.language.destroy"
                    @click="destroyLangPackage"
                  >
                    Remove
                  </button>
                </div>
              </form>

            </div>
          </div>

          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                Add Language
              </div>

              <form
                :action="$attrs.routes.language.store"
                id="addLang"
                data-save-message="Language was successfully installed."
                data-success-callback="removeAvailableLang"
                method="POST"
                @submit.prevent="submit"
              >
                <div class="form-group">
                  <label class="caption">
                    <select name="lang" class="form-select">
                      <template v-for="lang in available">
                        <option :value="lang.short" v-if="$page.props.installed.indexOf(lang.short) < 0">
                          {{ lang.full }}
                        </option>
                      </template>
                    </select>
                  </label>
                </div>

                <div class="form-group shift-right">
                  <button name="apply" class="btn blue">
                    Add
                  </button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>

      <Confirmation
        text="Do you really want to remove this language package?"
        ref="confirmation"
        okBtnClass="danger"
        okText="Remove"
        noText="Cancel"
      />
    </template>
  </Layout>
</template>

<script>

import Confirmation from "../../Shared/Confirmation.vue";
import Method from "../../Shared/Form/Method.vue";
import {FormMixin} from "../../Mixins/form-mixin";
import {showNotification} from "../../../libs/notifications";

export default {
  components: {Confirmation, Method},
  computed: {
    /**
     * Sorted list of available languages
     * @returns {*[]}
     */
    available() {
      let result = []
      for (let key in this.availableLanguages) {
        result.push({
          short: key,
          full: this.availableLanguages[key]
        })
      }
      return result.sort((a, b) => a.full.localeCompare(b.full));
    },
    /**
     * Sorted list of installed languages
     * @returns {*}
     */
    installed() {
      return this.$page.props.installed.sort((a, b) => a.localeCompare(b));
    }
  },
  data() {
    return {
      // Clone available languages object
      availableLanguages: {...this.$page.props.available}
    }
  },
  mixins: [FormMixin],
  name: "Settings/Language",
  methods: {
    /**
     * Remove language package click
     * @param e
     */
    destroyLangPackage(e) {
      const btn = $(e.target).closest('button')
      const lang = {
        short: btn.closest('#selectLang').find('select[name="main_language"]').val(),
        full: btn.closest('#selectLang').find('select[name="main_language"] option:selected').text().trim()
      }

      if ('en' === lang.short) {
        showNotification({
          type: 'error',
          text: ['Cannot remove default language.']
        })
      } else {
        const confirm = this.$refs.confirmation.open()

        confirm.then(res => res && this.request({
            method: 'delete',
            url: btn.data('url').replace(/0$/, lang.short),
            msg: `Language package "${lang.short}" was successfully removed.`,
            onSuccess: () => {
              this.availableLanguages[lang.short] = lang.full
              this.$page.props.installed = this.$page.props.installed.filter(e => e !== lang.short)
            }
          })
        )
      }
    },
    /**
     * Remove selected language from the list of available packages
     * @param response
     */
    removeAvailableLang(response) {
      this.$page.props.installed.push(response.data)
      delete this.availableLanguages[response.data]
    }
  }
}
</script>