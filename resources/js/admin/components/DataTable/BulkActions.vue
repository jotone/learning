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
<!--      <v-select-->
<!--        label="label"-->
<!--        :options="options"-->
<!--        :clearable="false"-->
<!--        :searchable="false"-->
<!--        :selectable="option => !option?.disabled"-->
<!--        v-model="selectedOption"-->
<!--      >-->
<!--        <template v-slot:option="option">-->
<!--          <span-->
<!--            :class="{'disabled-option': option.disabled || false}"-->
<!--            @click="option.hasOwnProperty('callback') ? option.callback() : () => {}"-->
<!--          >-->
<!--            {{ option.label }}-->
<!--          </span>-->
<!--        </template>-->
<!--      </v-select>-->
    </div>
  </div>
</template>

<script setup>
// Vue libs
import {ref, watch} from 'vue';

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
 * Clear all selected checkboxes
 * @param e
 * @param state
 */
const forceSelection = (e, state) => emit('clear', e.target.closest('.content-table-wrap').querySelector('tbody'), state)

const selectEvent = (a,b,c,d) => {
  console.log(a,b,c,d)
}

/*
 * Variables
 */
// View / Hide Bulk actions component
const show = ref(props.counter > 0);
// Total number of items
const totalItems = ref(props.total);
// Selected option
let selectedOption = ref(props.selected)

/*
 * Watchers
 */
watch(props, val => {
  show.value = val.counter > 0;
  totalItems.value = val.total;
})
</script>