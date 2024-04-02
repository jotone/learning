<template>
  <FilterDropdown :filters="filters"/>
</template>

<script setup>
import {FilterDropdown} from "../../../components/DataTable";

const filters = [
  {
    text: 'Category',
    value: 'category',
    type: 'multipleSelector',
  },
  {
    text: 'Creation Date',
    value: 'created_at',
    type: 'datePicker'
  },
  {
    text: 'Enrollment Number',
    value: 'enroll_number',
    type: 'number'
  },
  {
    text: 'Completion Rate',
    value: 'completion_rate',
    type: 'number'
  },
  {
    text: 'Status',
    value: 'status',
    type: 'booleanList'
  },
  {
    text: 'Language',
    value: 'lang',
    type: 'multipleSelector'
  },
  {
    text: 'Certificate',
    value: 'certificate',
    type: 'booleanList'
  },
  {
    text: 'Instructor',
    value: 'instructor',
    type: 'multipleSelector'
  },
  {
    text: 'Duration',
    value: 'duration',
    type: 'number'
  },
  {
    text: 'Course Level',
    value: 'course_level',
    type: 'number'
  },
  {
    text: 'Content Update',
    value: 'update_at',
    type: 'datePicker'
  },
  {
    text: 'Newly Released',
    value: 'published_at',
    type: 'datePicker'
  }
];
</script>