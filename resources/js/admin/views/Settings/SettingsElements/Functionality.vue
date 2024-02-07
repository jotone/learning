<template xmlns="http://www.w3.org/1999/html">
  <SettingsElement name="Functionality">
    <div class="row underline">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">Registration profile</span>
          <span class="about">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent porttitor velit mauris, vel pharetra sem tempor eget.
            Integer luctus, tortor a ultrices scel.
          </span>
        </label>
      </div>
      <div class="col-1-2">
        <ul class="options-list">
          <li>
            <SliderCheckbox
              name="enable_phone"
              text="Enable Phone"
              :checked="!!settings.enable_phone"
              @change="updateSettingsValue"
            />
          </li>
          <li>
            <SliderCheckbox
              name="enable_address"
              text="Enable Address"
              :checked="!!settings.enable_address"
              @change="updateSettingsValue"
            />
          </li>
          <li>
            <SliderCheckbox
              name="enable_signature"
              text="Enable User's Signature"
              :checked="!!settings.enable_signature"
              @change="updateSettingsValue"
            />
          </li>
          <li>
            <SliderCheckbox
              name="enable_shirt_size"
              text="Enable T-Shirt Size"
              :checked="!!settings.enable_shirt_size"
              @change="updateSettingsValue"
            />
          </li>
          <li>
            <SliderCheckbox
              name="enable_custom_question"
              text="Enable Custom Questions"
              :checked="!!settings.enable_custom_question"
              @change="updateSettingsValue"
            />
          </li>
        </ul>

        <ul class="options-list">
          <li>
            <label class="caption" style="margin: 20px 0">
              <span>Custom Question 1</span>
              <textarea
                class="form-text"
                name="custom_question_1"
                placeholder="Type your question here"
                v-model="settings.custom_question_1"
              >{{ settings.custom_question_1 }}</textarea>

              <CircleProgress :current="settings.custom_question_1?.length || 0" max="200"/>
            </label>
          </li>
          <li>
            <label class="caption" style="margin: 20px 0">
              <span>Custom Question 2</span>
              <textarea
                class="form-text"
                name="custom_question_2"
                placeholder="Type your question here"
                v-model="settings.custom_question_2"
              >{{ settings.custom_question_2 }}</textarea>

              <CircleProgress :current="settings.custom_question_2?.length || 0" max="200"/>
            </label>
          </li>
          <li>
            <label class="caption" style="margin: 20px 0">
              <span>Custom Question 3</span>
              <textarea
                class="form-text"
                name="custom_question_3"
                placeholder="Type your question here"
                v-model="settings.custom_question_3"
              >{{ settings.custom_question_3 }}</textarea>

              <CircleProgress :current="settings.custom_question_3?.length || 0" max="200"/>
            </label>
          </li>
        </ul>
      </div>
    </div>

    <div class="row underline">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">Search</span>
          <span class="about">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent porttitor velit mauris.</span>
        </label>
      </div>
      <div class="col-1-2">
        <div class="wide-wrap">
          <SliderCheckbox
            name="enable_search"
            text="Enable Search"
            :checked="!!settings.enable_search"
            @change="updateSettingsValue"
          />
        </div>

        <label class="caption" style="margin-top: 30px" v-if="settings.enable_search">
          <span>Search Title</span>
          <input class="form-input" name="search_title" placeholder="Add the search title" v-model="settings.search_title">
        </label>
      </div>
    </div>

    <div class="row underline">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">Curriculum Menu Type</span>
          <span class="about">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent porttitor velit mauris.</span>
        </label>
      </div>
      <div class="col-1-2">
        <select class="form-select" v-model="settings.curriculum_menu">
          <option value="0">Dropdown</option>
          <option value="1">Other</option>
        </select>
      </div>
    </div>

    <div class="row underline">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">Lesson Complete</span>
          <span class="about">Automatically mark the lesson complete when a user watches 90% of the video.</span>
        </label>
      </div>
      <div class="col-1-2">
        <div class="wide-wrap">
          <SliderCheckbox
            name="enable_lesson_complete"
            text="Enable Lesson Complete"
            :checked="!!settings.enable_lesson_complete"
            @change="updateSettingsValue"
          />
        </div>
      </div>
    </div>

    <div class="row underline">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">Zapier Integration</span>
          <span class="about">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent porttitor velit mauris, vel pharetra sem tempor eget.
            Integer luctus, tortor a ultrices scelerisque.
            <br><br>
            If you need help watch the <a href="https://www.loom.com/share/2b46052e55c049418ec1907ac5b68b2f">Training Video.</a>
            <br><br>
            <button class="btn blue" type="button" @click="settings.zapier_key = generateKey(33)">
              Generate Key
            </button>
          </span>
        </label>
      </div>
      <div class="col-1-2">
        <label class="caption wide-wrap">
          <span>Zapier API Key</span>
          <input class="form-input" name="zapier_key" placeholder="Add the key" v-model="settings.zapier_key">
        </label>
        <label class="caption">
          <span>Zapier URL</span>
          <input class="form-input" name="zapier_url" disabled :value="settings.site_url">
        </label>
        <label class="caption">
          <span>Zapier Invitation Link</span>
          <a class="text-bar-wrap" href="https://zapier.com/developer/public-invite/161900/5ee81194a294740f6c608de4548a03d8">
            https://zapier.com/developer/public-invite/161900/5ee81194a294740f6c608de4548a03d8/
          </a>
        </label>
      </div>
    </div>

    <div class="row">
      <div class="col-1-2">
        <label class="caption">
          <span class="title">Digistore24 Integration</span>
          <span class="about">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent porttitor velit mauris, vel pharetra sem tempor eget.
            Integer luctus, tortor a ultrices scelerisque.
          </span>
        </label>
      </div>

      <div class="col-1-2">
        <div class="wide-wrap">
          <SliderCheckbox
            name="enable_digistore"
            text="Activate Integration"
            :checked="!!settings.enable_digistore"
            @change="updateSettingsValue"
          />
        </div>

        <label class="caption" style="margin-top: 30px" v-if="settings.enable_digistore">
          <span>Digistore24 API key</span>
          <input class="form-input" name="digistore_key" placeholder="Add the key" v-model="settings.digistore_key">
        </label>
      </div>
    </div>
  </SettingsElement>
</template>

<script setup lang="ts">
import {CircleProgress, SliderCheckbox} from "../../../components/Form";
import SettingsElement from "./SettingsElement.vue";

const props = defineProps({
  settings: {
    type: Object,
    required: true
  }
});

/**
 * Function to update a value in the 'settings' object
 * @param value
 * @param field
 */
const updateSettingsValue = (value: any, field: string) => {
  props.settings[field] = value;
}

/**
 * Generates a random key of a specified length
 * @param length
 * @param key
 */
const generateKey = (length: number, key = ''): string => {
  const characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567897@#$%*';
  // Append a character from 'characters' at the random index to the 'key'
  for (let i = 0; i < length; i++) key += characters[Math.floor(Math.random() * characters.length)];
  return key;
}
</script>