<template>
  <label class="per-page-wrap">
    <span>Items per page</span>

    <select class="form-select" v-model="limit" @change="$emit('changeLimit', limit)">
      <option v-for="item in items" :value="item">{{ item }}</option>
    </select>
  </label>
</template>

<script setup>
import {ref} from "vue";

const props = defineProps({
  value: {
    type: Number,
    default: 25
  }
})

// Available options
const items = [10, 25, 50, 100];
// Items per page
let limit = ref(props.value)
</script>
