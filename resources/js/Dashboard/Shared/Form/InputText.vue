<template>
  <div class="form-group">
    <label class="caption">
      <span>{{ caption }}</span>
      <input
        autocomplete="off"
        class="form-input"
        :disabled="disabled"
        :min="min !== '' ? min : null"
        :max="max !== '' ? max : null"
        :name="name"
        :placeholder="caption.length ? caption + '...' : caption"
        :required="required"
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
    max: {
      type: [Number, String],
      default: ''
    },
    min: {
      type: [Number, String],
      default: ''
    },
    name: {
      type: String,
      default: ''
    },
    required: {
      type: Boolean,
      default: false
    },
    type: {
      type: String,
      default: 'text'
    },
    value: {
      type: [Number, String],
      default: ''
    }
  }
}
</script>