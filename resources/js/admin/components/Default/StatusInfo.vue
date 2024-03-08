<template>
  <div class="status-info-wrap" :class="type">
    {{ caption }}
  </div>
</template>

<script setup>
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