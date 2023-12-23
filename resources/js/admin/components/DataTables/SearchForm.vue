<template>
  <form action="?search=" class="search-form" name="search">
    <label>
      <input
        class="search-field"
        pattern=".{0}|.{3,}"
        type="search"
        :name="name"
        :placeholder="placeholder"
        :value="search"
      >
      <span @click.prevent="$emit('removeFilters')" v-if="showResetLink">Reset all filters</span>
      <button type="submit"><i class="icon search-icon"></i></button>
    </label>
  </form>
</template>

<script setup>
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
    default: true
  }
})
</script>