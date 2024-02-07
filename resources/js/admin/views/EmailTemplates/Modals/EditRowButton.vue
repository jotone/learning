<template>
  <button
    type="button"
    class="btn"
    :class="{active: value === align}"
  >
    <i class="icon" :class="icon"></i>
  </button>
</template>

<script setup>

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