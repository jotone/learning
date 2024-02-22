<template>
  <span class="circle-progress-wrap" :style="shift">
    <span class="inner-circle">
      <span v-if="showPercent">
        {{ percent }}%
      </span>

      <span v-if="showRemnant">
        {{ max - current }}
      </span>
    </span>
  </span>
</template>

<script setup>
import {ref, watch} from "vue";

const props = defineProps({
  current: {
    type: [Number, String],
    default: 0
  },
  max: {
    type: [Number, String],
    required: true
  },
  showPercent: {
    type: Boolean,
    default: false
  },
  showRemnant: {
    type: Boolean,
    default: false
  }
})
/*
 * Methods
 */
/**
 * Calculate the percentage based on the current value and the maximum value.
 * @param {string|number} value - The current value.
 * @return {number} - The calculated percentage.
 */
const percent = value => Math.min(100, (+value / Math.max(+props.max, 1)) * 100);

/**
 * Calculate the gradient shift for a given value.
 * @param {string|number} value - The value to calculate the gradient shift for.
 * @return {string} - The CSS property for the gradient shift.
 */
const calculateShift = value => `background: conic-gradient(#005aff ${percent(+value) * 3.6}deg, #ccdeff 0deg)`

/*
 * Variables
 */
// Initialize a reactive reference for the gradient shift
let shift = ref(calculateShift(props.current));

/*
 * Watchers
 */
// Watch for changes in the props.current value
watch(props, val => {
  // Update the shift value when props.current changes
  shift.value = calculateShift(val.current)
});
</script>