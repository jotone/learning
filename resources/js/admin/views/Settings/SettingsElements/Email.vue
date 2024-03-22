<template>
  <SettingsElement name="Email">
    <div class="row padding underline">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">SMTP Settings</span>
          <span class="about">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent porttitor velit mauris.
            <br><br><br>
            <b>*If you are using GSuite make sure to enable less secure apps from your GSuite administration and also on the account level.</b>
          </span>
        </label>
      </div>
      <div class="col-1-2">
        <ul class="options-list">
          <li>
            <label class="caption">
              <span>Username</span>
              <input
                class="form-input"
                name="smtp_username"
                placeholder="Set a username"
                v-model="settings.smtp_username"
              >
            </label>
          </li>
          <li>
            <label class="caption">
              <span>Password</span>
              <input
                class="form-input"
                name="smtp_password"
                type="password"
                placeholder="Set a password"
                v-model="settings.smtp_password"
              >
            </label>
          </li>
          <li>
            <label class="caption">
              <span>Host</span>
              <input class="form-input" name="smtp_password" placeholder="Set a host" v-model="settings.smtp_host">
            </label>
          </li>
          <li>
            <label class="caption">
              <span>Port</span>
              <input class="form-input" name="smtp_port" placeholder="Set a port" v-model="settings.smtp_port">
            </label>
          </li>
          <li>
            <label class="caption">
              <span>Encryption</span>
              <input
                class="form-input"
                name="smtp_encryption"
                placeholder="Set an encryption method"
                v-model="settings.smtp_encryption"
              >
            </label>
          </li>
          <li>
            <label class="caption">
              <span>From Email</span>
              <input
                class="form-input"
                name="smtp_from_address"
                placeholder="Set a form email"
                type="email"
                v-model="settings.smtp_from_address"
              >
            </label>
          </li>
          <li>
            <label class="caption">
              <span>From Name</span>
              <input
                class="form-input"
                name="smtp_from_name"
                placeholder="Set a form name"
                v-model="settings.smtp_from_name"
              >
            </label>
          </li>
          <li>
            <div class="form-row">
              <button type="button" class="btn blue" @click="saveSmtpSettings">
                Save and send test email
              </button>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div class="row padding underline">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">Email Global Settings</span>
          <span class="about">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent porttitor velit mauris.
          </span>
        </label>
      </div>
      <div class="col-1-2">
        <ul class="options-list">
          <li>
            <label class="caption">
              <span>Terms of Service Link</span>
              <input class="form-input"
                name="terms_of_service"
                placeholder="Set the Terms of Service Link..."
                v-model="settings.terms_of_service"
              >
            </label>
          </li>
          <li>
            <label class="caption">
              <span>Privacy Policy Link</span>
              <input class="form-input"
                name="privacy_policy"
                placeholder="Set the Privacy Policy Link..."
                v-model="settings.privacy_policy"
              >
            </label>
          </li>
          <li>
            <label class="caption">
              <span>Address</span>
              <input class="form-input"
                name="legal_address"
                placeholder="Set a legal address..."
                v-model="settings.legal_address"
              >
            </label>
          </li>
        </ul>
      </div>
    </div>

    <div class="row padding underline">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">Social Media Links on Footer</span>
          <span class="about">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent porttitor velit mauris.
          </span>
        </label>
      </div>
      <div class="col-1-2">
        <draggable
          class="options-list"
          handle=".sort-handle"
          itemKey="id"
          tag="ul"
          :list="socials.current"
          @change="socialMediaSort"
        >
          <template #item="{element}">
            <li>
              <label class="caption">
                <span>{{ element.caption }}</span>
                <input
                  class="form-input"
                  :class="{'has-controls': isAdmin}"
                  :name="`socials[${element.id}]`"
                  :placeholder="`Link to ${element.caption} page...`"
                  v-model="element.link"
                >
              </label>
              <template v-if="isAdmin">
                <div class="sort-handle">
                  <i class="icon double-hellip-icon"></i>
                </div>
                <div class="option-item-controls">
                  <a href="#" @click.prevent="socialMediaEdit(element)">
                    <i class="icon edit-icon"></i>
                  </a>
                  <a href="#" @click.prevent="socialMediaRemove(element.id)">
                    <i class="icon trash-icon"></i>
                  </a>
                </div>
              </template>
            </li>
          </template>
        </draggable>
        <button class="btn blue" v-if="isAdmin" @click.prevent="socialMediaAdd">
          Add Item
        </button>
      </div>
    </div>

    <div class="row padding underline">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">Template List</span>
          <span class="about">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent porttitor velit mauris.
          </span>
        </label>
      </div>

      <div class="col-1-2">
        <ul class="options-list">
          <li v-for="template in templates">
            <label class="caption">
              <input class="form-input readonly" style="padding-right: 50px" :value="template.title">
            </label>
            <div class="option-item-controls">
              <a :href="templateEdit(template.id)">
                <i class="icon edit-icon"></i>
              </a>
              <a href="#" @click.prevent="templateRemove(template.id)" v-if="isAdmin">
                <i class="icon trash-icon"></i>
              </a>
            </div>
          </li>
        </ul>
        <a class="btn blue" v-if="isAdmin" style="width: 150px" :href="page.props.routes.templates.create">
          Add Item
        </a>
      </div>
    </div>
  </SettingsElement>

  <Teleport to="body">
    <SocialMediaPopup ref="socialMediaModal" :socials="socials.list"/>
  </Teleport>
</template>

<script setup>
// Vue libs
import {inject, ref} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Components
import draggable from 'vuedraggable';
import SettingsElement from './SettingsElement.vue';
import SocialMediaPopup from '../Modals/SocialMediaPopup.vue';

const emit = defineEmits(['notify']);
// Assign the http request function
const request = inject('request')
// Page variables
const page = usePage()

const props = defineProps({
  isAdmin: {
    type: Boolean,
    default: false
  },
  settings: {
    type: Object,
    required: true
  },
  socials: {
    type: Object,
    required: true
  },
  templates: {
    type: Array,
    default: []
  }
});
/*
 * Methods
 */
/**
 * Opens a modal for adding a new social media entry.
 * @return {*}
 */
const socialMediaAdd = () => socialMediaModal.value.open().then(
  // Checks if the result is not null or false, indicating a successful submission.
  res => null !== res && false !== res && props.socials.current.push(res)
);
/**
 * Opens a modal for editing an existing social media entry. Updates the entry in socials with the new values.
 * @param social
 */
const socialMediaEdit = social => {
  // Opens the modal with the current social media entry data and waits for it to close.
  socialMediaModal.value.open(social).then(res => {
    // find and update the edited entry.
    for (let i = 0, n = props.socials.current.length; i < n; i++) {
      if (res.id === props.socials.current[i].id) {
        props.socials.current[i] = res;
        break
      }
    }
  })
}

/**
 * Remove a social media record identified by its 'id'.
 * @param {int} id
 */
const socialMediaRemove = id => {
  // Prompts the user for confirmation before proceeding with the deletion.
  const res = confirm('Do you really want to remove this Social Media record?')
  // Checks if the user confirmed the action.
  if (res) {
    // Makes an HTTP DELETE request to the server to remove the specified social media record.
    request({
      url: page.props.routes.socials.destroy.replace(/:id/, id),
      method: 'delete',
      onSuccess: response => {
        // Check if the response status code is 204 (No Content), indicating successful deletion.
        if (204 === response.status) {
          for (let i = 0, n = props.socials.current.length; i < n; i++) {
            // Checks if the current item's ID matches the deleted ID.
            if (id === props.socials.current[i].id) {
              // Removes the item from the list.
              props.socials.current.splice(i, 1)
              break;
            }
          }
        }
      }
    })
  }
}

/**
 * Sorting list handler
 */
const socialMediaSort = () => request({
  url: page.props.routes.socials.sort,
  method: 'patch',
  data: {
    list: props.socials.current.map((social, index) => ({
      id: social.id,
      position: index
    }))
  }
})

/**
 * Generate a link to the email-template edit page
 * @param id
 * @returns {*}
 */
const templateEdit = id => page.props.routes.templates.edit.replace(/:id/, id);
/**
 * Generate a link for email template remove
 * @param id
 */
const templateRemove = id => {
// Prompts the user for confirmation before proceeding with the deletion.
  const res = confirm('Do you really want to remove this Email template?');
  if (res) {
    // Makes an HTTP DELETE request to the server to remove the specified email template.
    request({
      url: page.props.routes.templates.destroy.replace(/:id/, id),
      method: 'delete',
      onSuccess: response => {
        // Check if the response status code is 204 (No Content), indicating successful deletion.
        if (204 === response.status) {
          for (let i = 0, n = props.templates.length; i < n; i++) {
            // Checks if the current item's ID matches the deleted ID.
            if (id === props.templates[i].id) {
              // Removes the item from the list.
              props.templates.splice(i, 1)
              break;
            }
          }
        }
      }
    })
  }
}
/**
 * Send request to save the SMTP settings.
 * @returns {Promise}
 */
const saveSmtpSettings = () => request({
  url: page.props.routes.settings.smtp,
  method: 'post',
  data: {
    smtp_username: props.settings.smtp_username,
    smtp_password: props.settings.smtp_password,
    smtp_host: props.settings.smtp_host,
    smtp_port: props.settings.smtp_port,
    smtp_encryption: props.settings.smtp_encryption,
    smtp_from_address: props.settings.smtp_from_address,
    smtp_from_name: props.settings.smtp_from_name
  },
  onSuccess: response => 200 === response.status && emit('notify', 'success', {
    msg: ['SMTP setting were successfully saved.']
  })
}).catch(e => null !== e?.response?.data?.errors && emit('notify', 'danger', e.response.data.errors))

/*
 * Variables
 */
// List of social media
const socialMediaModal = ref(null)
</script>