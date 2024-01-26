<template>
  <div class="custom-selector-wrap">
    <input
      class="custom-selector-input"
      readonly
      :placeholder="placeholder"
      :value="selected.text"
      @click="toggleDropdown"
    >
    <input style="display: none" v-model="selected.value">
    <div class="custom-selector-dropdown-list-wrap" v-if="showDropdown">
      <ul class="custom-selector-dropdown-list">
        <template v-for="option in options">
          <li v-html="row(option)" @click="changeValue(option)"></li>
        </template>
      </ul>
    </div>
  </div>
</template>

<script setup>
import {reactive, ref} from "vue";

const emit = defineEmits(['change'])

const props = defineProps({
  valueField: {
    type: String,
    default: null
  },
  textField: {
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
const changeValue = option => {
  selected.value = option.value;
  selected.text = option.text;
  emit('change', selected.value)
}
/**
 * Gets the text to be displayed for a given option
 * @param {object|string} option
 * @return {string}
 */
const optionText = option => null === props.valueField ? option : option[props.valueField];
/**
 * Generates the HTML for a row in the dropdown
 * @param {object|string} option
 * @return {string}
 */
const row = option => null === props.optionRow
  ? `<span>${optionText(option)}</span>`
  : props.optionRow(option)
/**
 * Toggles the visibility of the dropdown
 */
const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

/*
 * Variables
 */
// Current selected value
let selected = reactive({
  value: props.value.length ? props.value : optionText(props.options[0]),
  text: (props.value.length ? props.value : props.options[0].text).ucfirst()
})

// Dropdown visibility value
let showDropdown = ref(false)
</script>