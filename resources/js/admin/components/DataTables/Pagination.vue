<template>
  <nav class="pagination-wrap">
    <ul>
      <li>
        <a
          :href="props.current !== 1 ? `${path}` : '#'"
          @click.prevent="changePage"
        >
          <i class="icon double-chevron-icon"></i>
        </a>
      </li>
      <li>
        <a
          href="options.current_page !== 1 ? `${options.path}${buildUrlParams(options.current_page - 1)}` : '#'"
          @click.prevent="changePage"
        >
          <i class="icon chevron-icon"></i>
        </a>
      </li>
      <li>
        <a class="active" href="#">{{ 'options.current_page' }}</a>
      </li>
      <li><span>of {{ 'options.last_page' }}</span></li>
      <li>
        <a
          href="options.current_page !== options.last_page ? `${options.path}${buildUrlParams(options.current_page + 1)}` : '#'"
          @click.prevent="changePage"
        >
          <i class="icon mirror chevron-icon"></i>
        </a>
      </li>
      <li>
        <a
          href="options.current_page !== options.last_page ? `${options.path}${buildUrlParams(options.last_page)}` : '#'"
          @click.prevent="changePage"
        >
          <i class="icon mirror double-chevron-icon"></i>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script setup lang="ts">

import {PropType, ref} from "vue";
import {encodeUriQuery} from "../../libs/RequestHelper";
import {FiltersInterface} from "../../../contracts/FiltersInterface";

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
    default: 25,
  },
  total: {
    type: Number,
    default: 0
  },
})

const path = ref(window.location.origin + window.location.pathname)

const query = encodeUriQuery(props.filters);
console.log(path.value)
console.log(query)
const changePage = () => {}
</script>