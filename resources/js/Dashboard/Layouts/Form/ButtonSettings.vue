<template>
  <div class="buttons-settings-wrap">
    <ul class="button-settings-bookmarks">
      <li
        v-for="(item, key) in value"
        :class="{active: tab === key}"
        :data-state="key"
        @click="viewBookmark"
      >
        {{ key === 'normal' ? caption : ':' + key}}
      </li>
    </ul>

    <div
      class="button-settings-list"
      v-for="(item, key) in value"
      :class="{active: tab === key}"
      :data-state="key"
    >
      <div class="form-group" v-for="(prop, field) in item">
        <label class="caption">
          <!-- TODO: Make fields translations -->

          <span>{{ field }}:</span>

          <input
            autocomplete="off"
            class="form-input"
            data-jscolor="{format:'hex', previewSize:40}"
            :name="`${name}[${key}][${field}]`"
            :value="item[field]"
          >
        </label>
      </div>
    </div>
  </div>
</template>

<script>

import "@eastdesire/jscolor/jscolor"

export default {
  name: "ButtonSettings",
  props: ["caption", "name", "value"],
  data() {
    return {
      tab: 'normal'
    }
  },
  methods: {
    viewBookmark(e) {
      this.tab = $(e.target).closest('li').data('state')
    }
  }
}
</script>