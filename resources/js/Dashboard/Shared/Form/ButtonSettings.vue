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
      <template v-for="(prop, field) in item">
        <InputColor :caption="__('common.' + field)" :name="`${name}[${key}][${field}]`" :value="item[field]"/>
      </template>
    </div>
  </div>
</template>

<script>
import InputColor from "./InputColor.vue";

export default {
  name: "ButtonSettings",
  components: {InputColor},
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