<template>
  <Layout>
    <slot></slot>
  </Layout>
</template>

<script setup>
import Layout from "./Layout.vue";

defineOptions({layout: Layout})
</script>
