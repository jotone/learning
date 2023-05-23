<template>
  <Layout>
    <template v-slot:content>
      <div class="page-content-wrap">
        <div class="row">
          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                {{ __('settings.language.select_main') }}
              </div>

              <form
                id="selectLang"
                method="POST"
                :action="$attrs.routes.settings.update"
                :data-save-message="__('settings.language.success')"
                @submit.prevent="submit"
              >
                <input name="_method" type="hidden" value="PATCH">
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
                    {{ __('common.save') }}
                  </button>
                  <button
                    name="remove"
                    class="btn"
                    type="button"
                    :data-url="$attrs.routes.language.destroy"
                    @click="destroyLangPackage"
                  >
                    {{ __('common.remove') }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <div class="col-1-2">
            <div class="card">
              <div class="card-title">
                {{ __('settings.language.add_lang') }}
              </div>

              <form
                id="addLang"
                data-success-callback="removeAvailableLang"
                method="POST"
                :action="$attrs.routes.language.store"
                :data-save-message="__('settings.language.installed')"
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
                    {{ __('common.add') }}
                  </button>
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
                        <span>{{ __('settings.language.denotation') }}</span>
                      </th>
                      <th>
                        <span>{{ __('common.value') }}</span>
                      </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(value, field) in translations">
                      <td>
                        <span v-html="field.ucfirst()"></span>
                      </td>
                      <td>
                        <InputText @keyup="changeTranslationValue" :name="field" :value="value" :title="origin[field]"/>
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
        okBtnClass="danger"
        ref="confirmation"
        :okText="__('common.remove')"
        :text="__('settings.language.remove_msg')"
      />
    </template>
  </Layout>
</template>

<script>

import debounce from "debounce"
import Confirmation from "../../Shared/Confirmation.vue";
import {FormMixin} from "../../Mixins/form-mixin";
import {showNotification} from "../../../libs/notifications";
import InputText from "../../Shared/Form/InputText.vue";

export default {
  components: {InputText, Confirmation},
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
     * Change translation value
     */
    changeTranslationValue: debounce(e => {
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
        msg: this.__('settings.language.field_saved', data.key)
      })
    }, 500),
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
          text: [this.__('settings.language.remove_err')]
        })
      } else {
        const confirm = this.$refs.confirmation.open()

        confirm.then(res => res && this.request({
            method: 'delete',
            url: btn.data('url').replace(/:lang/, lang.short),
            msg: this.__('settings.language.removed_msg', lang.short),
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
  },
  name: "Settings/Language"
}
</script>