<template>
  <div class="form-group">
    <label class="caption">
      <span>{{ caption }}</span>
      <input
        autocomplete="off"
        class="form-input"
        :disabled="disabled"
        :name="name"
        :placeholder="caption.length ? caption + '...' : caption"
        :type="type"
        :value="value"
        @input="$emit('update:value', $event.target.value)"
      >
    </label>
  </div>
</template>

<script>

export default {
  name: "InputText",
  props: {
    caption: {
      type: String,
      default: ''
    },
    disabled: {
      type: Boolean,
      default: false
    },
    name: {
      type: String,
      default: ''
    },
    type: {
      type: String,
      default: 'text'
    },
    value: {
      type: String,
      default: ''
    }
  }
}
</script>