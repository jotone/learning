<template>
  <button type="button" class="btn editor-button" :class="{active: value === align}">
    <i class="icon" :class="icon"></i>
  </button>
</template>

<script setup>
// Get component properties
const props = defineProps({
  icon: {
    type: String,
    required: true
  },
  align: {
    type: [Boolean, String],
    default: true
  },
  value: {
    type: [Boolean, String],
    default: false
  }
})
</script>