<template>
  <header>
    <div class="page-name-wrap">
      <h1>Settings</h1>

      <a class="btn" @click.prevent="saveSettings($attrs.routes.settings.update)">
        <span>Save Changes</span>
      </a>
    </div>
  </header>

  <Notifications/>

  <ul class="dropdown-list-wrap">
    <MainSettings :settings="mainSettingsForm"/>
    <Functionality :settings="functionalityForm"/>
  </ul>
</template>

<script setup>
// Vue libs
import {reactive} from "vue";
import {usePage} from "@inertiajs/vue3";
// Other Libs
import axios from "axios";
import {Notification} from "../../libs/Notification";
// Components
import Functionality from "./SettingsElements/Functionality.vue";
import MainSettings from "./SettingsElements/MainSettings.vue";
import Notifications from "../../components/Default/Notifications.vue";
// Layout
import Layout from "../../shared/Layout.vue";

defineOptions({layout: Layout})

// Page variables
const page = usePage()

const mainSettingsForm = reactive({
  site_url: page.props.data.site_url,
  site_title: page.props.data.site_title,
  default_timezone: page.props.data.default_timezone,
  site_custom_url: page.props.data.site_custom_url,
})

const functionalityForm = reactive({
  site_url: page.props.data.site_url,
  curriculum_menu: page.props.data.curriculum_menu,
  enable_address: page.props.data.enable_address,
  enable_custom_question: page.props.data.enable_custom_question,
  enable_help_center: page.props.data.enable_help_center,
  enable_lesson_complete: page.props.data.enable_lesson_complete,
  enable_phone: page.props.data.enable_phone,
  enable_search: page.props.data.enable_search,
  enable_shirt_size: page.props.data.enable_shirt_size,
  enable_signature: page.props.data.enable_signature,
  enable_sticky_menu: page.props.data.enable_sticky_menu,
  custom_question_1: page.props.data.custom_question_1,
  custom_question_2: page.props.data.custom_question_2,
  custom_question_3: page.props.data.custom_question_3,
  search_title: page.props.data.search_title,
  digistore_enable: page.props.data.digistore_enable,
  digistore_key: page.props.data.digistore_key,
  zapier_key: page.props.data.zapier_key,
})

const saveSettings = url => {
  const headers = {
    headers: {
      'Authorization': 'Bearer ' + page.props.auth.apiToken
    }
  }
  const forms = [mainSettingsForm, functionalityForm]
  let requests = []
  for (let i = 0, n = forms.length; i < n; i++) {
    requests.push(axios.patch(url, forms[i], headers))
  }
  Promise.all(requests)
    .then(() => Notification.success('Settings has been updated. Your changes have been saved!'))
    .catch(e => {
      console.error(e)
    })
}
</script>