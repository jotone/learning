<template>
  <div class="multiple-selector--wrap" v-click-outside="hideAvailable">
    <i class="icon search search-icon"></i>
    <i class="icon close close-icon" @click="clearAll"></i>

    <ul class="multiple-selector__selected" @click="toggleDropdown">
      <li v-for="(item, i) in result">
        <span>{{ item[field] }}</span>
        <i class="icon close-icon" @click="unselect(i)"></i>
      </li>
    </ul>

    <div class="multiple-selector__available scrollbar" :class="{active: showDropdown}">
      <ul>
        <li v-for="(option, i) in availableList" @click="select(i)">
          <span>{{ option[field] }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import {ref} from "vue";
// Assign the function to emit
const emit = defineEmits(['change'])
// Get component properties
const props = defineProps({
  selected: {
    type: Array,
    default: []
  },
  options: {
    type: Array,
    default: []
  },
  label: {
    type: String,
    default: 'name'
  }
});

/*
 * Methods
 */
/**
 * Build a list of available items
 * @returns {Array}
 */
const buildOptions = () => {
  const selected = new Set(result.value.map(item => item.name));
  return props.options.filter(option => !selected.has(option[field.value]));
}

/**
 * Show or hide the list of available options
 * @param {null|boolean} status
 */
const toggleDropdown = (status = null) => {
  showDropdown.value = null !== status ? status : !showDropdown.value && availableList.value.length;
}

/**
 * Force hide the list of available options
 */
const hideAvailable = () => toggleDropdown(false)

/**
 * Add an option to the list of selected options
 * @param {int} i
 */
const select = i => {
  result.value.push(availableList.value[i]);
  availableList.value = buildOptions();
  emit('change', result.value);
}

/**
 * Remove an option from the list of selected options
 * @param i
 */
const unselect = i => {
  result.value.splice(i, 1);
  availableList.value = buildOptions();
  emit('change', result.value);
}

/**
 * Remove all options from the list of selected options
 */
const clearAll = () => {
  result.value = [];
  availableList.value = buildOptions();
  toggleDropdown(false)
  emit('change', result.value);
}
/*
 * Variables
 */
const field = ref(props.label)

let result = ref(props.selected)
let availableList = ref(buildOptions())

let showDropdown = ref(false)
</script>