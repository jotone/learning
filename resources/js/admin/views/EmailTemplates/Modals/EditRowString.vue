<template>
  <li :class="{active: isActive}" :data-tag="element.tag">
    <span :style="stylesToString">{{ element.text }}</span>
  </li>
</template>

<script setup>
// Vue libs
import {computed} from "vue";

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  },
  element: {
    type: Object,
    required: true
  }
})
/**
 * Convert an element style object to CSS string
 * @type {ComputedRef<string>}
 */
const stylesToString = computed(() => props.element?.attributes?.style
  ? Object.entries(props.element.attributes.style).map(([rule, value]) => `${rule}:${value};`).join('')
  : ''
)
</script>