<template>
  <tr>
    <td class="static">
      <div class="static-fields">
        <div class="bulk-select">
          <input type="checkbox" name="checkEl" @change="bulkActionCheckboxChange">
        </div>
        <a class="image-container">
          <img :src="courseImage" v-if="Object.keys(props.course.img_url).length" alt="">
        </a>
      </div>
    </td>

    <template v-for="column in columns">
      <td v-if="column.field === 'status'">
        <StatusInfo :caption="statusValue.text" :type="statusValue.type"/>
      </td>

      <td v-else-if="column.field === 'certificate_enable'">
        <span class="bool-value-wrap">
          <i v-if="!course[column.field]" class="icon false-value close-icon"></i>
          <i v-if="course[column.field]" class="icon true-value ok-icon"></i>
        </span>
      </td>

      <td v-else-if="['published_at', 'created_at', 'updated_at'].indexOf(column.field) >= 0">
        <span>{{ convertDate(course[column.field]) }}</span>
      </td>

      <td v-else-if="column.field === 'categories'">
        <p v-for="category in course[column.field]">
          {{ category.name }}
        </p>
      </td>

      <td v-else>
        <a :href="editUrl">{{ course[column.field] }}</a>
      </td>
    </template>
  </tr>
</template>

<script setup lang="ts">
// Vue libs
import {computed, inject} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Interface
import {ColumnInterface} from '../../../../contracts/ColumnInterface';
// Components
import StatusInfo from '../../../components/Default/StatusInfo.vue';

// Implement function to convert dates into the proper view
const convertDate = inject('convertDate');

// Assign the function to emit
const emit = defineEmits(['changeBulkCheckbox']);

// Use data passed to the page
const page = usePage();

// Get component properties
const props = defineProps({
  course: Object,
  columns: Array<ColumnInterface>
});

/*
 * Computed
 */
/**
 * Get course image. First the smallest one
 * @return {string}
 */
const courseImage = computed(() => Object.keys(props.course.img_url).length
  ? props.course.img_url?.small || props.course.img_url?.large || props.course.img_url?.original
  : ''
)

/**
 * Link for the role edit page
 * @return {string}
 */
const editUrl = computed((): string => page.props.routes.course.settings.replace(/:id/, props.course.id))

/**
 * Get the status options by the course status value
 * @return {string}
 * @throws RangeError
 */
const statusValue = computed(() => {
  switch (props.course.status) {
    case 'active':
      return {
        type: 'success',
        text: 'Active'
      }
    case 'coming_soon':
      return {
        type: 'warning',
        text: 'Coming Soon'
      }
    case 'draft':
      return {
        type: null,
        text: 'Draft'
      }
    default:
      console.error(props.course)
      throw new RangeError('Unknown course status.')
  }
});

/*
 * Methods
 */
/**
 * Bulk actions checkbox change event
 */
const bulkActionCheckboxChange = e => emit('changeBulkCheckbox', e)
</script>