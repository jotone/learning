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
    <Email :settings="emailForm" :socials="$attrs.socials.current" @addSocialMedia="socialMediaModalView"/>
  </ul>

  <SocialMediaPopup ref="socialMediaModal" :socials="$attrs.socials.list"/>
</template>

<script setup>
// Vue libs
import {reactive, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
// Other Libs
import axios from "axios";
import {Notification} from "../../libs/Notification";
// Components
import Email from "./SettingsElements/Email.vue";
import Functionality from "./SettingsElements/Functionality.vue";
import MainSettings from "./SettingsElements/MainSettings.vue";
import Notifications from "../../components/Default/Notifications.vue";
import SocialMediaPopup from "./SocialMediaPopup.vue";
// Layout
import Layout from "../../shared/Layout.vue";

defineOptions({layout: Layout})

// Page variables
const page = usePage()

console.log(page.props)

const socialMediaModal = ref(null)
const socialMediaModalView = () => {
  socialMediaModal.value.open().then(res => {
    console.log(res)
  })
}

const fillForm = keys => {
  let obj = {}
  for (let key in page.props.data) {
    if (keys.indexOf(key) >= 0) {
      obj[key] = page.props.data[key]
    }
  }
  return obj
}

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

const mainSettingsForm = reactive(fillForm([
  'site_url',
  'site_title',
  'default_timezone',
  'site_custom_url'
]))

const functionalityForm = reactive(fillForm([
  'site_url',
  'curriculum_menu',
  'digistore_key',
  'enable_address',
  'enable_custom_question',
  'enable_digistore',
  'enable_help_center',
  'enable_lesson_complete',
  'enable_phone',
  'enable_search',
  'enable_shirt_size',
  'enable_signature',
  'enable_sticky_menu',
  'custom_question_1',
  'custom_question_2',
  'custom_question_3',
  'search_title',
  'zapier_key'
]))

const emailForm = reactive(fillForm([
  'smtp_username',
  'smtp_password',
  'smtp_host',
  'smtp_port',
  'smtp_encryption',
  'smtp_from_address',
  'smtp_from_name',
  'terms_of_service',
  'privacy_policy',
  'legal_address',
]))

</script>