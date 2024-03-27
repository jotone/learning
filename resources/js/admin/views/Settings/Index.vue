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
    <Email
      :isAdmin="$attrs.auth.role.level === 0"
      :settings="emailForm"
      :socials="$attrs.socials"
      :templates="$attrs.templates"
      @notify="callNotification"
    />
    <Coaches :roles="$attrs.roles" :routes="$attrs.routes"/>
  </ul>
</template>

<script setup>
// Vue libs
import {inject, reactive} from "vue";
import {usePage} from "@inertiajs/vue3";
// Other Libs
import {Notification} from "../../libs/Notification";
// Components
import Coaches from './SettingsElements/Coaches.vue';
import Email from "./SettingsElements/Email.vue";
import Functionality from "./SettingsElements/Functionality.vue";
import MainSettings from "./SettingsElements/MainSettings.vue";
import Notifications from "../../components/Default/Notifications.vue";
// Layout
import Layout from "../../shared/Layout.vue";

defineOptions({layout: Layout})

// Assign the http request function
const request = inject('request')
// Page variables
const page = usePage()

/*
 * --------------- Forms ---------------
 */
/**
 * Fills a form object with data from page.props.data based on a list of keys.
 * @param keys
 * @return {Object}
 */
const fillForm = keys => {
  let obj = {}
  for (let key in page.props.data) {
    // If the key is in the provided list, add it to the obj.
    if (keys.indexOf(key) >= 0) {
      obj[key] = page.props.data[key]
    }
  }
  return obj
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
  'enable_lesson_complete',
  'enable_phone',
  'enable_search',
  'enable_shirt_size',
  'enable_signature',
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
  'legal_address'
]))

/**
 * Saves settings by making API requests for each form and showing a success notification upon completion.
 * @param url
 */
const saveSettings = url => {
  // Prepares a list of forms to be submitted.
  const forms = [mainSettingsForm, functionalityForm, emailForm]
  let requests = []
  for (let i = 0, n = forms.length; i < n; i++) {
    // Creating a patch request for each and adding it to the request array.
    requests.push(request({
      url: url,
      method: "patch",
      data: forms[i]
    }))
  }
  // Wait for all requests are complete.
  Promise.all(requests)
    .then(() => Notification.success('Settings has been updated. Your changes have been saved!'))
    .catch(e => console.error(e))
}

/*
 * --------------- Notifications---------------
 */

/**
 * Custom notifications
 * @param type
 * @param messages
 */
const callNotification = (type, messages) => messages.forEach(
  messageGroup => messageGroup.forEach(message => Notification[type](message))
);
</script>