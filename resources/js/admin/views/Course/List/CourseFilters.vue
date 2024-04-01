<template>
  <div class="filters-list-wrap">
    <CustomSelector
      ref="actionSelector"
      :options="available"
      :template="option => `<span>${option.text}</span>`"
      placeholder="Bulk Actions"
      @change="executeCallback"
    />
  </div>
</template>

<script setup>
import {computed, ref} from "vue";
import {CustomSelector} from "../../../components/Form/index.js";

const executeCallback = () => {
  console.log(111)
}

const filters = [
  {
    text: 'Category',
    type: 'multipleSelector',
  },
  {
    text: 'Creation Date',
    type: 'datePicker'
  },
  {
    text: 'Enrollment Number',
    type: 'number'
  },
  {
    text: 'Feedback Count',
    type: 'number'
  },
  {
    text: 'Completion Rate',
    type: 'number'
  },
  {
    text: 'Status',
    type: 'booleanList'
  },
  {
    text: 'Language',
    type: 'multipleSelector'
  },
  {
    text: 'Certificate',
    type: 'booleanList'
  },
  {
    text: 'Instructor',
    type: 'multipleSelector'
  },
  {
    text: 'Duration',
    type: 'number'
  },
  {
    text: 'Course Level',
    type: 'number'
  },
  {
    text: 'Content Update',
    type: 'datePicker'
  },
  {
    text: 'Newly Released',
    type: 'datePicker'
  }
]

let selected = ref([]);

let available = computed(() => {
  let result = []
  for (let i in filters) {
    if (selected.value.indexOf(i) < 0) {
      result.push(filters[i])
    }
  }
  return result;
})
</script>