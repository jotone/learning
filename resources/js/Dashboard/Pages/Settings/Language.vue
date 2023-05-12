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
                  <button name="apply" class="btn blue">Add</button>
                </div>
              </form>

            </div>
          </div>
        </div>

        <div class="row">
          <div class="card col-1">
            <div class="buttons-settings-wrap short">
              <ul class="button-settings-bookmarks">
                <li
                  v-for="lang in $attrs.installed"
                  :class="{active: lang === active.lang}"
                  :data-lang="lang"
                  @click="viewLang"
                >
                  {{ $attrs.available[lang].ucfirst() }}
                </li>
              </ul>
            </div>

            <div class="language-files-wrap">
              <ul class="language-files-list-wrap">
                <li
                  v-for="file in $attrs.files"
                  :class="{active: file === active.file}"
                  :data-file="file"
                  @click="viewFile"
                >
                  {{ file.ucfirst() }}
                </li>
              </ul>

              <div class="table-group translations-table">
                <div class="table-wrap">
                  <table>
                    <thead>
                    <tr>
                      <th>
                        <span>Denotation</span>
                      </th>
                      <th>
                        <span>Value</span>
                      </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(value, field) in translations">
                      <td>
                        <span v-html="field.ucfirst()"></span>
                      </td>
                      <td>
                        <InputText :name="field" :value="value" :title="origin[field]"/>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
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

import debounce from "debounce"
import Confirmation from "../../Shared/Confirmation.vue";
import Method from "../../Shared/Form/Method.vue";
import {FormMixin} from "../../Mixins/form-mixin";
import {showNotification} from "../../../libs/notifications";
import InputText from "../../Shared/Form/InputText.vue";

export default {
  components: {InputText, Confirmation, Method},
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
      active: {
        lang: 'en',
        file: 'auth'
      },
      // Clone available languages object
      availableLanguages: {...this.$page.props.available},
      // Origin english field value
      origin: [],
      // Translated field values
      translations: []
    }
  },
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
            url: btn.data('url').replace(/:lang/, lang.short),
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
     * Get file translations for selected language
     */
    fileTranslations() {
      axios.get(this.$attrs.routes.language.show.replace(/:lang/, this.active.lang).replace(/:file/, this.active.file))
        .then(response => {
          if (200 === response.status) {
            let translations = {}, origin = {}
            for (let field in response.data.list) {
              const value = response.data.list[field]
              const originValue = response.data.origin[field]
              if (typeof value === 'object') {
                for (let key in value) {
                  const innerKey = `<b>${field}</b>.${key}`
                  translations[innerKey] = value[key]
                  origin[innerKey] = originValue[key]
                }
              } else {
                translations[field] = value
                origin[field] = originValue
              }
            }
            this.translations = translations
            this.origin = origin
          }
        })
    },
    /**
     * Remove selected language from the list of available packages
     * @param response
     */
    removeAvailableLang(response) {
      this.$page.props.installed.push(response.data)
      delete this.availableLanguages[response.data]
    },
    /**
     * Select file
     * @param e
     */
    viewFile(e) {
      this.active.file = $(e.target).closest('li').attr('data-file')
      this.fileTranslations()
    },
    /**
     * Select language
     * @param e
     */
    viewLang(e) {
      this.active.lang = $(e.target).closest('li').attr('data-lang')
      this.fileTranslations()
    }
  },
  mixins: [FormMixin],
  mounted() {
    this.active.lang = $('.buttons-settings-wrap li.active').attr('data-lang')
    this.active.file = $('.language-files-list-wrap li.active').attr('data-file')
    this.fileTranslations()

    $('.translations-table').on('keyup', 'input[type="text"]', debounce(e => {
      const _this = $(e.target).closest('input')
      const data = {
        lang: this.active.lang,
        file: this.active.file,
        key: _this.attr('name'),
        value: _this.val().trim()
      };
      this.request({
        method: 'patch',
        url: this.$attrs.routes.language.update,
        data: data,
        msg: `Translation for the field "${data.key}" was successfully saved.`
      })
    }, 500))
  },
  name: "Settings/Language"
}
</script>