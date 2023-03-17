<template>
  <div class="form-group">
    <label class="caption">
      <span>{{ caption }}:</span>

      <input
        autocomplete="off"
        class="form-input"
        data-jscolor="{format:'hex', previewSize:40}"
        :name="name"
        :value="getValue"
      >

    </label>
  </div>
</template>

<script>

import "@eastdesire/jscolor/jscolor"

export default {
  name: "InputColor",
  props: ["caption", "name", "value"],
  computed: {
    getCaption() {
      return this.caption ?? ''
    },
    getValue() {
      return typeof this.value === 'string' ? this.value : ''
    }
  },
  mounted() {
    jscolor.install()
  }
}
</script>