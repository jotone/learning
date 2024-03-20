<template>
  <li>
    <a :href="editUrl" :class="{active: item.active}">
      <i class="icon" :class="item.img"></i>
      <span>{{ item.name }}</span>
    </a>
  </li>
</template>
<script setup lang="ts">
// Vue libs
import {computed, PropType} from "vue";
import {usePage} from "@inertiajs/vue3";
// Interface
import {TopMenuItemInterface} from "../../../contracts/TopMenuItemInterface";

const page =  usePage();

const props = defineProps({
  item: {
    type: Object as PropType<TopMenuItemInterface>,
    required: true
  }
})

const editUrl = computed(() => props.item.route.replace(/:id/, page.props.course.id))
</script>