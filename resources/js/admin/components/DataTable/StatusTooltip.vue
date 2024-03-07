<template>
  <div
    v-if="show"
    class="status-tooltip-wrap"
    :style="{left: options.left + 'px', top: options.top + 'px'}"
    @mouseleave="hideTooltip"
    v-click-outside="hideTooltip"
  >
    <slot/>
  </div>
</template>

<script setup lang="ts">
// Vue libs
import {nextTick, reactive, ref} from 'vue';

/*
 * Methods
 */
/**
 * Hide tooltip box
 */
const hideTooltip = async () => await nextTick(() => toggleShow(false, {left: 0, top: 0}))

/**
 * Change "show" value
 * @param status
 * @param coordinates
 */
const toggleShow = (status: boolean, coordinates) => {
  show.value = status;
  options = coordinates
}

/*
 * Variables
 */
const show = ref(false)

let options = reactive({
  left: 0,
  top: 0
})

defineExpose({toggleShow})
</script>