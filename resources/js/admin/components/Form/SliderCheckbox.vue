<template>
  <label class="slider-checkbox-wrap">
    <slot name="before"/>
    <span class="slider-checkbox-container" :class="{checked: state}">
      <span class="slider-checkbox-circle"></span>
    </span>
    <input type="checkbox" :name="name" @change="toggleStatus">
    <span class="slider-checkbox-text" v-html="text"></span>
    <slot name="after"/>
  </label>
</template>

<script setup>
// Vue lib
import {ref} from "vue";
// Assign the function to emit
const emit = defineEmits(['change'])
// Get component properties
const props = defineProps({
  checked: {
    type: Boolean,
    default: false
  },
  name: {
    type: String,
    required: true
  },
  text: {
    type: String,
    default: ''
  }
})

// Default checkbox state
const state = ref(props.checked)
// Invert the current value of 'state' and emits a 'change' event
const toggleStatus = () => {
  state.value = !state.value
  emit('change', state.value, props.name)
}
</script>