<template>
  <div class="column-selector-wrap">
    <ul class="column-selector-section-list">
      <template v-for="section in sections">
        <ColumnSelectorSection :section="section" @changeColumnStatus="toggleColumn"/>
      </template>
    </ul>
  </div>

  <div class="column-selector-reposition-wrap">
    <div class="column-selector-reposition-caption">
      Column Reposition
    </div>

    <ColumnSelectorReposition :list="columns" @change="pageColumnSort"/>
  </div>
</template>

<script setup lang="ts">
// Vue libs
import {inject} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Interfaces
import {ColumnSectionInterface} from '../../../contracts/ColumnSectionInterface.js';
import ColumnSelectorSection from './ColumnSelectorSection.vue';
import ColumnSelectorReposition from './ColumnSelectorReposition.vue';

// Assign the function to emit
const emit = defineEmits(['changeColumnStatus'])
// Assign the http request function
const request = inject('request');
// Page variables
const page = usePage()

const props = defineProps({
  columns: {
    type: Array,
    default: []
  },
  sections: {
    type: Array<ColumnSectionInterface>,
    required: true
  }
})
/*
 * Methods
 */
/**
 * Sorting columns handler
 */
const pageColumnSort = () => request({
  url: page.props.routes.page_columns.sort,
  method: 'patch',
  data: {
    list: props.columns.map((column, index) => ({
      id: column.id,
      position: index
    }))
  }
})
/**
 * Check the page column changed its status and pass the execution results to the parent component
 * @param section
 * @param field
 * @param value
 */
const toggleColumn = (section: string, field: string, value: boolean) => emit('changeColumnStatus', section, field, value)
</script>