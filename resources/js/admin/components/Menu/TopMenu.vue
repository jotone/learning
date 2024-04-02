<template>
  <div class="top-menu">
    <ul>
      <TopMenuItem v-for="item in menu" :item="item"/>
    </ul>
  </div>
</template>

<script setup lang="ts">
// Interface
import {TopMenuItemInterface} from "../../../contracts/TopMenuItemInterface";
// Component
import TopMenuItem from "./TopMenuItem.vue";

const props = defineProps({
  menu: {
    type: Array<TopMenuItemInterface>,
    required: true
  }
})
</script>