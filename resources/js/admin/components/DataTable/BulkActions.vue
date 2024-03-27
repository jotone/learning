<template>
  <div class="bulk-selector-wrap" v-if="show">
    <div class="bulk-select-counters">
      <span>{{ counter }} selected</span>
      <button class="btn blue--inverse" v-if="total !== counter" @click="forceSelection($event, true)">
        <span>Select All {{ totalItems }} Courses</span>
      </button>
      <button class="btn blue" v-else @click="forceSelection($event, false)">
        <span>Clear Selection</span>
      </button>
    </div>

    <div class="bulk-select-selector">
      <CustomSelector
        ref="actionSelector"
        :options="options"
        :template="option => `<span>${option.text}</span>`"
        placeholder="Bulk Actions"
        @change="executeCallback"
      />
    </div>
  </div>
</template>

<script setup>
// Vue libs
import {ref, watch} from 'vue';
import {CustomSelector} from "../Form";

// Assign the function to emit
const emit = defineEmits(['clear']);

// Get component properties
const props = defineProps({
  counter: {
    type: Number,
    default: 0
  },
  options: {
    type: Array,
    required: true
  },
  selected: {
    type: [Boolean, Number, String],
    default: null
  },
  total: {
    type: Number,
    default: 0
  }
})

/*
 * Methods
 */
/**
 * Run the selected option callback from the bulk options list
 * @param selected
 */
const executeCallback = selected => {
  for (let i = 0, n = props.options.length; i < n; i++) {
    const option = props.options[i]
    if (option.value === selected && typeof option.callback === 'function') {
      actionSelector.value.reset();
      option.callback();
      break;
    }
  }
}

/**
 * Clear all selected checkboxes
 * @param e
 * @param state
 */
const forceSelection = (e, state) => emit('clear', e.target.closest('.content-table-wrap').querySelector('tbody'), state)

/*
 * Variables
 */
// View / Hide Bulk actions component
const show = ref(props.counter > 0);
// Total number of items
const totalItems = ref(props.total);
// Bulk action selector reference
const actionSelector = ref(null);
/*
 * Watchers
 */
watch(props, val => {
  show.value = val.counter > 0;
  totalItems.value = val.total;
})
</script>