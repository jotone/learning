<template>
  <div class="form-group">
    <label class="caption">
      <span>{{ caption }}:</span>

      <input
        autocomplete="off"
        class="form-input"
        data-jscolor="{format:'hex', previewSize:40}"
        :name="name"
        :value="value"
      >

    </label>
  </div>
</template>

<script>

import "@eastdesire/jscolor/jscolor"

export default {
  name: "InputColor",
  props: {
    caption: {
      type: String,
      default: ''
    },
    name: {
      type: String,
      default: ''
    },
    value: {
      type: [Number, String],
      default: ''
    }
  },
  mounted() {
    jscolor.install()
  }
}
</script>