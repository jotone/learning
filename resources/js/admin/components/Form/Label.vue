<template>
  <label :class="{caption: !inline, 'caption--inline': inline}">
    <span v-if="caption.length">{{ caption }}</span>

    <slot></slot>
  </label>
</template>

<script setup>
const props = defineProps({
  caption: {
    type: String,
    default: ''
  },
  inline: {
    type: Boolean,
    default: false
  }
})
</script>