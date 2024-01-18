<template>
  <header>
    <div class="page-name-wrap">
      <h1>Settings</h1>

      <a class="btn" @click.prevent="saveSettings($attrs.routes.settings.update)">
        <span>Save Changes</span>
      </a>
    </div>
  </header>

  <ul class="dropdown-list-wrap">
    <MainSettings :settings="mainSettingsForm"></MainSettings>
  </ul>
</template>

<script setup>
// Vue libs
import {reactive} from "vue";
import {usePage} from "@inertiajs/vue3";
// Other Libs
import axios from "axios";
// Components
import MainSettings from "./SettingsElements/MainSettings.vue";
// Layout
import Layout from "../../shared/Layout.vue";

defineOptions({layout: Layout})

// Page variables
const page = usePage()

const mainSettingsForm = reactive({
  site_url: page.props.data.site_url.value,
  site_title: page.props.data.site_title.value,
  default_timezone: page.props.data.default_timezone.value,
  site_custom_url: page.props.data.site_custom_url.value,
})

const saveSettings = url => {
  axios.patch(url, mainSettingsForm, {
    headers: {
      'Authorization': 'Bearer ' + page.props.auth.apiToken
    }
  }).then(response => {
    console.log(response)
  })
}
</script>