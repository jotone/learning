<template>
  <div class="sidebar-wrap" v-if="show">
    <div class="sidebar-close" @click="toggleShow(false)">
      <i class="icon close-icon"></i>
    </div>

    <div class="sidebar-caption" v-if="caption.length">{{ caption }}</div>

    <slot/>
  </div>
</template>

<script setup>
import {ref} from 'vue';

const props = defineProps({
  caption: {
    type: String,
    default: ''
  }
})

/*
 * Methods
 */
const toggleShow = (state) => {
  show.value = state;
}
/*
 * Variables
 */
let show = ref(false);

defineExpose({toggleShow})
</script>