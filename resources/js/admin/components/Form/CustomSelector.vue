<template>
  <div class="custom-selector">
    <input
      class="custom-selector--input"
      readonly
      :placeholder="placeholder"
      :value="result === null ? options[0][label] : result.text"
      @click="toggleDropdown"
    >
    <div class="custom-selector--dropdown" v-if="showDropdown">
      <ul>
        <template v-for="option in options">
          <li
            :class="{disabled: option.hasOwnProperty('disabled') && option.disabled}"
            v-html="template(option)"
            @click="select(option)"
          ></li>
        </template>
      </ul>
    </div>
  </div>
</template>

<script setup>
// Vue libs
import {reactive, ref} from "vue";

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
  selected: {
    type: [Number, Boolean, String],
    default: null
  }
})
/*
 * Methods
 */
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
 */
const select = option => {
  if (!option.disabled) {
    result.value = option.value;
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