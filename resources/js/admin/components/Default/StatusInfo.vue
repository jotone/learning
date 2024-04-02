<template>
  <div class="status-info" :class="type">
    {{ caption }}
  </div>
</template>

<script setup>
// Get component properties
const props = defineProps({
  type: {
    type: String,
    default: null
  },
  caption: {
    type: String,
    required: true
  }
});
</script>