<template>
  <SettingsElement name="Email">
    <div class="row underline">
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
              <input class="form-input" name="smtp_username" placeholder="Set a username" v-model="settings.smtp_username">
            </label>
          </li>
          <li>
            <label class="caption">
              <span>Password</span>
              <input class="form-input" name="smtp_password" placeholder="Set a password" v-model="settings.smtp_password">
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
              <input class="form-input" name="smtp_encryption" placeholder="Set an encryption method" v-model="settings.smtp_encryption">
            </label>
          </li>
          <li>
            <label class="caption">
              <span>From Email</span>
              <input class="form-input" name="smtp_from_address" placeholder="Set a form email" v-model="settings.smtp_from_address">
            </label>
          </li>
          <li>
            <label class="caption">
              <span>From Name</span>
              <input class="form-input" name="smtp_from_name" placeholder="Set a form name" v-model="settings.smtp_from_name">
            </label>
          </li>
        </ul>
      </div>
    </div>

    <div class="row underline">
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
              <input class="form-input" name="terms_of_service" placeholder="Set the Terms of Service Link" v-model="settings.terms_of_service">
            </label>
          </li>
          <li>
            <label class="caption">
              <span>Privacy Policy Link</span>
              <input class="form-input" name="privacy_policy" placeholder="Set the Privacy Policy Link" v-model="settings.privacy_policy">
            </label>
          </li>
          <li>
            <label class="caption">
              <span>Address</span>
              <input class="form-input" name="legal_address" placeholder="Set a legal address" v-model="settings.legal_address">
            </label>
          </li>
        </ul>
      </div>
    </div>

    <div class="row underline">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">Social Media Links on Footer</span>
          <span class="about">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent porttitor velit mauris.
          </span>
        </label>
      </div>
      <div class="col-1-2">
        <ul class="options-list">
          <li v-for="social in socials">
            <label class="caption">
              <span>{{ social.caption }}</span>
              <input
                class="form-input"
                :class="{'has-controls': isAdmin}"
                :name="`socials[${social.id}]`"
                :placeholder="`Link to ${social.caption} page`"
                v-model="social.link"
              >
            </label>
            <template v-if="isAdmin">
              <div class="sort-handle">
                <i class="icon double-hellip-icon"></i>
              </div>
              <div class="option-item-controls">
                <a href="#" @click.prevent="emit('editSocialMedia', social)">
                  <i class="icon edit-icon"></i>
                </a>
                <a href="#" @click.prevent="emit('removeSocialMedia', social.id)">
                  <i class="icon trash-icon"></i>
                </a>
              </div>
            </template>
          </li>
        </ul>
        <button class="btn blue" @click="emit('addSocialMedia')" v-if="isAdmin">
          Add Item
        </button>
      </div>
    </div>
  </SettingsElement>
</template>

<script setup>
import SettingsElement from "./SettingsElement.vue";

const emit = defineEmits(['addSocialMedia', 'editSocialMedia', 'removeSocialMedia'])

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
    type: Array,
    default: []
  }
});
</script>