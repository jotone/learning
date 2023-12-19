<template>
  <li :class="{active: isActive}">
    <a :href="item.route">
      <i class="icon" :class="item.img"></i>
      <span>{{ item.name }}</span>
    </a>
  </li>
</template>

<script setup lang="ts">
import { computed, PropType } from 'vue'
import { SideMenuItemInterface } from "../../../contracts/SideMenuItemInterface";

// Component properties
const props = defineProps({item: Object as PropType<SideMenuItemInterface>})

// Check the menu item is active
const isActive = computed(() => {
  const pathName = window.location.pathname;
  const location = pathName.at(-1) === '/' ? pathName.slice(0, -1) : pathName
  return props.item.route === location;
})
</script>