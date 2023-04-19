<template>
  <div class="form-group">
    <label class="caption">
      <span>{{ caption }}</span>
      <select :name="name" class="form-select">
        <option :value="i" v-for="(text, i) in options" :selected="i === value">
          {{ text.ucfirst() }}
        </option>
      </select>
    </label>
  </div>
</template>

<script>
export default {
  name: "Selector",
  props: {
    caption: {
      type: String,
      default: ''
    },
    name: {
      type: String,
      default: ''
    },
    options: {
      type: Object,
      required: true
    },
    value: {
      type: [String, Number],
      default: null
    }
  }
}
</script>