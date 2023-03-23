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
                action="#"
                method="POST"
              >
                <Method value="PATCH"/>
                <div class="form-group">
                  <label class="caption">
                    <select name="lang" class="form-select">
                      <option
                        v-for="lang in $page.props.installed"
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
                  <button name="remove" class="btn" type="button">
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
                data-success-callback="removeLang"
                method="POST"
                @submit.prevent="submit"
              >
                <div class="form-group">
                  <label class="caption">
                    <select name="lang" class="form-select">
                      <template v-for="(name, lang) in availableLanguages">
                        <option :value="lang" v-if="$page.props.installed.indexOf(lang) < 0">
                          {{ name }}
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
    </template>
  </Layout>
</template>

<script>
import Layout from "../../Shared/Layout.vue";
import Method from "../../Shared/Form/Method.vue";
import {FormMixin} from "../../Mixins/form-mixin";

export default {
  data() {
    return {
      // Clone available languages object
      availableLanguages: {...this.$page.props.available}
    }
  },
  name: "Settings/Language",
  components: {Method, Layout},
  mixins: [FormMixin],
  methods: {
    removeLang(response) {
      this.$page.props.installed.push(response.data)
      delete this.availableLanguages[response.data]
    }
  }
}
</script>