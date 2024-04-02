<template>
  <div>
    <CustomSelector
      ref="actionSelector"
      :key="filterCounter"
      :options="available"
      :template="option => `<span>${option.text}</span>`"
      :selected="available.length ? available[0].value : null"
      placeholder="Bulk Actions"
      @change="setFilters"
    />
  </div>
</template>

<script setup>
import {reactive, ref} from "vue";
import {CustomSelector} from "../Form/index.js";

const props = defineProps({
  filters: {
    type: Array,
    required: true
  }
})

/**
 * Force a CustomSelector to rerender
 */
const forceRerender = () => filterCounter.value++;

/**
 * Remove selected fields from available
 * @return {Object}
 */
const siftSelected = () => Object.values(props.filters).filter(filter => !selected.value.includes(filter.value));

const setFilters = index => {
  output = {};
  if (!selected.value.includes(index)) {
    selected.value.push(index);
  }

  let position = 0;
  for (let i in props.filters) {
    const filter = props.filters[i];

    if (selected.value.includes(filter.value)) {
      output[filter.value] = {
        text: filter.text,
        type: filter.type,
        index: +i,
        pos: position
      }
      position++;
    }
  }

  available.value = siftSelected()

  forceRerender();
}

// Custom Selector Component re-render helper
const filterCounter = ref(0);
// Selected items
let selected = ref([]);
// Available items
let available = ref(siftSelected());

let output = reactive({});
</script>