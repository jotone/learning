<template>
  <form action="?search=" class="search-form" name="search" @submit.prevent="submitForm">
    <label>
      <input
        class="search-field"
        pattern=".{0}|.{3,}"
        type="search"
        :name="name"
        :placeholder="placeholder"
        v-model="string"
      >
      <span @click.prevent="$emit('removeFilters')" v-if="showResetLink">Reset all filters</span>
      <button type="submit"><i class="icon search-icon"></i></button>
    </label>
  </form>
</template>

<script setup>
import {ref} from "vue";

const emit = defineEmits(['runSearch'])

// Component properties
const props = defineProps({
  name: {
    type: String,
    default: "search"
  },
  placeholder: {
    type: String,
    default: ""
  },
  search: {
    type: String,
    default: ""
  },
  showResetLink: {
    type: Boolean,
    default: false
  }
})

let string = ref(props.search)

/**
 * Search handler
 * @return {false|void}
 */
const submitForm = () => (string.value.length >= 2 || string.value.length === 0) && emit('runSearch', string.value)
</script>