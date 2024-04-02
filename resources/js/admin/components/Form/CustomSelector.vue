<template>
  <div class="custom-selector" v-click-outside="hideSelector">
    <input
      class="custom-selector__input"
      readonly
      :placeholder="placeholder"
      :value="result === null ? options[0][label] : result.text"
      @click="toggleDropdown"
    >
    <div class="custom-selector__dropdown" v-if="showDropdown">
      <ul class="scrollbar">
        <template v-for="(option, i) in options">
          <li
            :class="{disabled: option.hasOwnProperty('disabled') && option.disabled}"
            v-html="template(option)"
            @click="select(option, i)"
          ></li>
        </template>
      </ul>
    </div>
  </div>
</template>

<script setup>
// Vue libs
import {reactive, ref, watch} from "vue";

// Assign the function to emit
const emit = defineEmits(['change'])

// Get component properties
const props = defineProps({
  label: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    default: []
  },
  template: {
    type: Function,
    required: true
  },
  settings: {
    type: Object,
    default: {
      exclude: false
    }
  }
})
/*
 * Methods
 */
/**
 * Close the custom selector on miss click
 * @param e
 */
const hideSelector = e => {
  if (null === e.target.closest('.custom-selector__dropdown')) {
    showDropdown.value = false;
  }
}
/**
 * Build the result of the selected value
 * @returns {object|null}
 */
const prepareSelectedItem = () => {
  if (!props.options.length) {
    return null
  }
  const option = selectedOption.value == null
    ? props.options[0]
    : props.options.find(item => item.value === selectedOption.value) || props.options[0];
  return {value: option.value, text: option[props.label]};
}
/**
 * Reset selector, bind a null value
 */
const reset = () => {
  selectedOption.value = null;
  result = prepareSelectedItem();
}
/**
 * Set new selector values on change event
 * @param {object} option
 * @param {int} i
 */
const select = (option, i) => {
  if (!option.disabled) {
    result.value = option.hasOwnProperty('value') ? option.value : i;
    result.text = option.text;
    showDropdown.value = false;
    emit('change', result.value)
  }
}

/**
 * Toggles the visibility of the dropdown
 */
const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

/*
 * Variables
 */
// Dropdown visibility value
let showDropdown = ref(false);

let selectedOption = ref(props.selected);
// Get selected item
let result = reactive(prepareSelectedItem())

defineExpose({reset});
</script>