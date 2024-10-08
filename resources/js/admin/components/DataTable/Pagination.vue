<template>
  <nav class="pagination-wrap">
    <ul>
      <li>
        <a
          v-if="props.current !== 1"
          :href="`${path}?${modifyQuery({page: 1})}`"
          @click.prevent="changePage(1)"
        >
          <i class="icon mirror double-chevron-icon"></i>
        </a>
        <span v-else>
          <i class="icon mirror double-chevron-icon"></i>
        </span>
      </li>
      <li>
        <a
          v-if="props.current !== 1"
          :href="`${path}?${modifyQuery({page: props.current - 1})}`"
          @click.prevent="changePage(props.current - 1)"
        >
          <i class="icon mirror chevron-icon"></i>
        </a>
        <span v-else>
          <i class="icon mirror chevron-icon"></i>
        </span>
      </li>
      <li>
        <a class="active" href="#">{{ props.current }}</a>
      </li>
      <li>
        <strong>of {{ props.last }}</strong>
      </li>
      <li>
        <a
          v-if="props.current !== props.last"
          :href="`${path}?${modifyQuery({page: props.current + 1})}`"
          @click.prevent="changePage(props.current + 1)"
        >
          <i class="icon chevron-icon"></i>
        </a>
        <span v-else>
          <i class="icon chevron-icon"></i>
        </span>
      </li>
      <li>
        <a
          v-if="props.current !== props.last"
          :href="`${path}?${modifyQuery({page: props.last})}`"
          @click.prevent="changePage(props.last)"
        >
          <i class="icon double-chevron-icon"></i>
        </a>
        <span v-else>
          <i class="icon double-chevron-icon"></i>
        </span>
      </li>
    </ul>
  </nav>
</template>

<script setup lang="ts">
// Vue libs
import {PropType, ref} from "vue";
// Other libs
import {decodeUriQuery, encodeUriQuery} from "../../libs/RequestHelper";
// Interfaces
import {FiltersInterface} from "../../../contracts/FiltersInterface";

// Assign the function to emit
const emit = defineEmits(['changePage'])

// Get component properties
const props = defineProps({
  current: {
    type: Number,
    default: 1
  },
  filters: {
    type: Object as PropType<FiltersInterface>,
    required: true
  },
  last: {
    type: Number,
    default: 1
  },
  perPage: {
    type: Number,
    default: 25
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
 * Modify parameter of the URI query
 * @param {object} obj
 * @return {string}
 */
const modifyQuery = (obj: object): string => encodeUriQuery(Object.assign({}, query, obj))

/**
 * Pagination element click handler
 * @param {number} page
 */
const changePage = (page: number) => emit('changePage', Object.assign({}, props.filters, {page: page}));

/*
 * Variables
 */
// Page absolute path
const path = ref(window.location.origin + window.location.pathname)
// Build a pagination query
let query = decodeUriQuery(window.location.search)
if (!query.hasOwnProperty('page')) {
  query.page = props.filters.page
}
</script>