<template>
  <div class="sidebar" v-if="show">
    <div class="sidebar__close" @click="toggleShow(false)">
      <i class="icon close-icon"></i>
    </div>

    <div class="sidebar__caption" v-if="caption.length">{{ caption }}</div>

    <slot/>
  </div>
</template>

<script setup lang="ts">
// Vue lib
import {ref} from 'vue';
// Get component properties
const props = defineProps({
  caption: {
    type: String,
    default: ''
  }
})

/*
 * Methods
 */

/**
 * Show or hide sidebar depends on state value
 * @param {boolean} state
 */
const toggleShow = (state: boolean) => {
  show.value = state;
}
/*
 * Variables
 */
let show = ref(false);

defineExpose({toggleShow})
</script>