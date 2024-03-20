<template>
  <label class="caption">
    <span v-if="caption.length">{{ caption }}</span>

    <slot></slot>
  </label>
</template>

<script setup>
const props = defineProps({
  caption: {
    type: String,
    default: ''
  }
})
</script>