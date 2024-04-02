<template>
  <span class="circle-progress" :style="shift">
    <span class="inner-circle">
      <span v-if="showPercent">{{ percent(current) }}%</span>

      <span v-if="showRemnant" v-html="remnant()"></span>
    </span>
  </span>
</template>

<script setup>
// Vue libs
import {ref, watch} from "vue";
// Get component properties
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
 * Calculate the gradient shift for a given value.
 * @param {string|number} value - The value to calculate the gradient shift for.
 * @return {string} - The CSS property for the gradient shift.
 */
const calculateShift = value => {
  switch (status.value) {
    case 1:
      color.value = '#ef9400'
      break;
    case 2:
      color.value = '#ff2947'
      break;
    default:
      color.value = '#005aff';
  }
  return `background: conic-gradient(${color.value} ${percent(+value) * 3.6}deg, #ccdeff 0deg)`
}

/**
 * Get current status
 * @returns {UnwrapRef<number>}
 */
const getStatus = () => status.value;

/**
 * Calculate the percentage based on the current value and the maximum value.
 * @param {string|number} value - The current value.
 * @return {number} - The calculated percentage.
 */
const percent = value => {
  return parseInt(Math.min(100, (+value / Math.max(+props.max, 1)) * 100));
}

/**
 * Calculate the remnant characters value
 * @returns {number|string}
 */
const remnant = () => {
  let diff = props.max - props.current;
  if (props.max > 0) {
    const percent = diff / props.max

    if (percent >= 0 && percent <= 0.1) {
      status.value = 1;
    } else if (percent <= 0) {
      status.value = 2;
    } else {
      status.value = 0;
    }

    if (status.value > 0) {
      return `<span style="color: ${color.value}">${diff}</span>`;
    }
  }
  return ''
}

/*
 * Variables
 */
// Progress color
let color = ref('#005aff')
// Progress status
let status = ref(0)
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

defineExpose({getStatus})
</script>