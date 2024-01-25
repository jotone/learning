<template>
  <div class="custom-selector-wrap">
    <div class="custom-selector-element">
      <input
        class="custom-selector-input"
        :placeholder="placeholder"
        v-model="selected"
        @change="emit('update:value', $event.target.value.trim())"
      >
    </div>
    <div class="custom-selector-down-arrow">
      <i class="icon chevron-icon"></i>
    </div>
    <div class="custom-selector-dropdown-list-wrap">
      <ul class="custom-selector-dropdown-list">
        <li v-for="option in options" v-html="row(option)"></li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import {ref} from "vue";

const emit = defineEmits(['update:value'])

const props = defineProps({
  field: {
    type: String,
    default: null
  },
  placeholder: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    default: []
  },
  optionRow: {
    type: Function,
    default: null
  },
  value: {
    type: String,
    default: ''
  }
})

/*
 * Methods
 */
const row = option => null === props.optionRow
  ? (null === props.field ? option : option[props.field])
  : props.optionRow(option)

/*
 * Variables
 */
let selected = ref()
</script>