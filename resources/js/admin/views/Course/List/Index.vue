<template>
  <header>
    <div class="page-name-wrap">
      <h1>Courses</h1>

      <a class="btn" :href="$attrs.routes.create">
        <i class="icon book-plus-icon"></i>
        <span>Create Course</span>
      </a>
    </div>
  </header>

  <Notifications/>

  <div class="card">
    <div class="content-table-wrap">
      <div class="content-table-controls">
        <div class="filters-list-wrap">
          <select class="form-select">
            <option>Category</option>
            <option>Creation Date</option>
          </select>
        </div>

        <SearchForm
          placeholder="Search for a course by the name or url…"
          :search="$attrs.filters?.search"
          @runSearch="runSearch"
        />

        <div class="column-selector-wrap">
          <i class="icon column-selector-icon"></i>
        </div>
      </div>

      <div class="table-container">
        <table>
          <thead>
          <tr>
            <th class="static">
              <div class="static-fields">
                <TableHeadCol field="name" :filters="filters" name="Name" @changeDirection="changeDirection"/>
              </div>
            </th>
          </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Vue libs
import {inject, reactive, ref} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Other Libs
import {decodeUriQuery, encodeUriQuery} from "../../../libs/RequestHelper";
import Notifications from '../../../components/Default/Notifications.vue';
// Interfaces
import {FiltersInterface} from "../../../../contracts/FiltersInterface";
// Layout
import Layout from "../../../shared/Layout.vue";
import {getFilters, SearchForm, TableHeadCol} from '../../../components/DataTable/index.js';

defineOptions({layout: Layout})

// Get content roles function
const requestGraphQL = inject('requestGraphQL')

// Page variables
const page = usePage()

/*
 * Methods
 */
/**
 * GraphQL query string to get roles list
 * @param {FiltersInterface} filters
 * @returns {string}
 */
const listQuery = (filters: FiltersInterface): string => `{courses(
  per_page:${filters.per_page},
  order_by:"${filters.order.by}",
  order_dir:"${filters.order.dir}",
  page:${filters.page},
  search:"${filters.search}"
) {
  total per_page last_page has_more_pages current_page data {
    id name url img_url category_id instructor_id lang status optional_duration users_count created_at updated_at category {id name} instructor {id email}
  }
}}`

/**
 * Change order
 * @param {any} order
 */
const changeDirection = (order: any) => {
  filters.order = order;
  getList(filters)
}

/**
 * Change per page items number
 * @param {number} limit
 */
const changeLimit = (limit: number) => {
  filters.per_page = limit
  getList(filters)
}

/**
 * Search item results
 * @param {string} search
 */
const runSearch = (search: string) => {
  filters.search = search;
  getList(filters)
}

/**
 * Send request to get a role list
 * @param {FiltersInterface} filters
 * @param {null|function} callback
 */
const getList = (filters: FiltersInterface, callback?: Function) =>
  requestGraphQL(page.props.routes.api, listQuery(filters))
    .then(response => {
      list.value = response.data.data.courses;
      typeof callback === 'function' && callback(filters)
    })

/*
 * Variables
 */
// Data-table items list
let list = ref([]);
// Decoded URI query
const query = decodeUriQuery(window.location.search)
// Page filters list
let filters = reactive(getFilters(query))

// Load roles
getList(filters)
</script>