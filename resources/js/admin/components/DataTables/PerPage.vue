<template>
  <label class="per-page-wrap">
    <span>Items per page</span>

    <select class="form-select" >
      <option v-for="item in items" :value="item">{{ item }}</option>
    </select>
  </label>
</template>

<script setup>
const items = [10, 25, 50, 100]
</script>
