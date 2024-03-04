<template>
  <SettingsElement name="Main Settings">
    <div class="row padding">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">Site URL</span>
          <span class="about">Add the URL you want to have for the site</span>
        </label>
      </div>
      <div class="col-1-2">
        <input disabled class="form-input" :value="settings.site_url"/>
      </div>
    </div>

    <div class="row padding">
      <div class="col-1-2">
        <label for="site_title" class="caption">
          <span class="title">System Name</span>
          <span class="about">Add the URL you want to have for the site</span>
        </label>
      </div>
      <div class="col-1-2">
        <input
          id="site_title"
          class="form-input"
          placeholder="System Name..."
          v-model="settings.site_title"
        />
      </div>
    </div>

    <div class="row padding">
      <div class="col-1-2">
        <label for="default_timezone" class="caption">
          <span class="title">Timezone</span>
          <span class="about">Sets the timezone</span>
        </label>
      </div>
      <div class="col-1-2">
        <select id="default_timezone" class="form-select" v-model="settings.default_timezone">
          <optgroup v-for="(countries, timezone) in timezones" :label="timezone">
            <option v-for="country in countries" :value="timezone">
              {{ country }}
            </option>
          </optgroup>
        </select>
      </div>
    </div>

    <div class="row padding">
      <div class="col-1-2">
        <div class="wide-wrap">
          <SliderCheckbox
            name="site_custom_url_enable"
            text="Custom Domain"
            :checked="!!settings.site_custom_url.length"
            @change="toggleCustomDomain"
          />
        </div>
        <label class="caption" v-if="enableCustomDomain">
          <span class="about">
            Go to your domain DNS provider and create a CNAME record with name <a :href="settings.site_url">{{ settings.site_url }}</a>
            and target <a :href="settings.site_url">{{ settings.site_url }}</a>.
            <br><br>
            When you complete changing your DNS please click Submit. Our team will finish the process on our side within 24 working hours.
          </span>
        </label>
      </div>
      <div class="col-1-2">
        <input
          v-if="enableCustomDomain"
          class="form-input"
          placeholder="Custom Domain..."
          :value="settings.site_custom_url || settings.site_url"
          @input="setCustomUrl"
        />
      </div>
    </div>
  </SettingsElement>
</template>

<script setup>
// Vue libs
import {ref} from "vue";
// Other Libs
import momentTimezone from "moment-timezone";
// Components
import SettingsElement from "./SettingsElement.vue";
import SliderCheckbox from "../../../components/Form/SliderCheckbox.vue";

const props = defineProps({
  settings: {
    type: Object,
    required: true
  }
});

/*
 * Variables
 */
// Mark the custom domain is enable
let enableCustomDomain = ref(!!props.settings.site_custom_url.length)
/**
 * TODO: make it work
 * @param value
 */
const toggleCustomDomain = value => {
  enableCustomDomain.value = value;
}
/*
 * Methods
 */
/**
 * Map each timezone to its offset
 * @type {object}
 */
const timezones = momentTimezone.tz.names().reduce((acc, timezone) => {
  const time = momentTimezone.tz(timezone).format('Z');
  if (!acc[time]) {
    acc[time] = [];
  }
  acc[time].indexOf(timezone) < 0 && acc[time].push(timezone);
  return acc;
}, {});

/**
 * Set custom url
 * @param e
 */
const setCustomUrl = e => {
  const value = e.target.value.trim();
  props.settings.site_custom_url = value === props.settings.site_url ? '' : value;
}
</script>