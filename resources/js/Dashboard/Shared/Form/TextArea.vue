<template>
  <div class="form-group">
    <label class="caption">
      <span>{{ caption }}</span>
      <textarea
        class="form-textarea"
        :name="name"
        :placeholder="placeholder"
      >{{ value }}</textarea>
    </label>
  </div>
</template>

<script>
export default {
  name: "TextArea",
  props: {
    caption: {
      type: String,
      default: ''
    },
    name: {
      type: String,
      required: true
    },
    placeholder: {
      type: String,
      default: ''
    },
    value: {
      type: String,
      default: ''
    }
  }
}
</script>