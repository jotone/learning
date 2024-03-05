<template>
  <div class="status-info-wrap" :class="type">
    {{ caption }}
  </div>
</template>

<script setup>
const props = defineProps({
  type: {
    type: String,
    required: true
  },
  caption: {
    type: String,
    required: true
  }
});
</script>