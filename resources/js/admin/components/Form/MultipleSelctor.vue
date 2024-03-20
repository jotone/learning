<template>
  <v-select
    :label="label"
    :options="options"
  >
    <template v-slot:option="option">
      <span @click="console.log(option)">{{ option[label] }}</span>
    </template>
  </v-select>
</template>

<script setup>
// Vue select css
import 'vue-select/dist/vue-select.css';

const props = defineProps({
  options: {
    type: Array,
    required: true
  },
  label: {
    type: String,
    default: 'name'
  }
})
</script>