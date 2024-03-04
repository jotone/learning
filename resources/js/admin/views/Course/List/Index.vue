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

        <div class="column-selector-button-wrap" @click="toggleSidebar">
          <i class="icon column-selector-icon"></i>
        </div>
      </div>

      <div class="table-container">
        <table>
          <thead>
          <tr>
            <th class="static">
              <div class="static-fields">
                <div class="bulk-select"><input type="checkbox" name="checkAll"></div>
                <TableHeadCol name="Thumbnail"/>
              </div>
            </th>
            <th v-for="column in columns">
              <TableHeadCol
                :field="column.field"
                :filters="filters"
                :name="column.name"
                @changeDirection="changeDirection"
              />
            </th>
          </tr>
          </thead>
        </table>
      </div>

      <div class="page-controls-wrap">
        <PerPage :value="filters.per_page" @changeLimit="changeLimit"/>
        <Pagination
          v-if="list.last_page > 1"
          :current="list.current_page"
          :filters="filters"
          :last="list.last_page"
          :perPage="list.per_page"
          :total="list.total"
          @changePage="changePage"
        />
      </div>
    </div>
  </div>

  <Sidebar ref="sidebar" caption="Choose Visible Columns">
    <ColumnSelector :sections="$attrs.sections" :columns="columns" @changeColumnStatus="toggleColumn"/>
  </Sidebar>
</template>

<script setup lang="ts">
// Vue libs
import {inject, reactive, ref} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Other Libs
import {decodeUriQuery, encodeUriQuery} from '../../../libs/RequestHelper';
import Notifications from '../../../components/Default/Notifications.vue';
// Interfaces
import {ColumnSectionInterface} from '../../../../contracts/ColumnSectionInterface';
import {FiltersInterface} from '../../../../contracts/FiltersInterface';
// Components
import {getFilters, Pagination, PerPage, SearchForm, TableHeadCol} from '../../../components/DataTable/index.js';
import ColumnSelector from '../../../components/DataTable/ColumnSelector.vue';
import Sidebar from '../../../components/Default/Sidebar.vue';
// Layout
import Layout from '../../../shared/Layout.vue';

defineOptions({layout: Layout})

// Get content roles function
const request = inject('request');
const requestGraphQL = inject('requestGraphQL');

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
 * Click pagination element
 * @param {FiltersInterface} filters
 */
const changePage = (filters: FiltersInterface) => getList(filters, (filters: FiltersInterface) => {
  let state = {page: filters.page}
  if (filters.search.length) {
    state.search = filters.search
  }
  // Change uri state
  window.history.pushState(window.location.origin + window.location.pathname, "", '?' + encodeUriQuery(state))
})

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

/**
 * Check the page column changed its status
 * @param section
 * @param field
 * @param value
 */
const toggleColumn = (section: string, field: string, value: boolean) => {
  let found = false;
  for (let i = 0, n = page.props.sections.length; i < n; i++) {
    if (page.props.sections[i].slug === section) {
      const columns = page.props.sections[i].columns;
      for (let j = 0, m = columns.length; j < m; j++) {
        if (columns[j].field === field) {
          columns[j].enable = value;
          request({
            url: page.props.routes.page_columns.update.replace(/:id/, columns[j].id),
            method: 'patch',
            data: {enable: value}
          })
          break;
        }
      }
    }
    if (found) {
      break;
    }
  }

  columns.value = activeColumns(page.props.sections)
}

/**
 * Show or hide sidebar
 * @param status
 */
const toggleSidebar = (status: boolean = true) => {
  sidebar.value.toggleShow(status);
}
/*
 * Methods
 */
/**
 * Get enabled columns list
 * @param sections
 */
let activeColumns = (sections: Array<ColumnSectionInterface>) => Object.values( // Reset the result array indexes
  // Go through every section
  sections.reduce((result, section) => {
    // Go through every enabled column in the section
    section.columns.filter(col => col.enable)
      // Fill the result array with column values
      .reduce((sum, col) => result[col.table_position] = {
        id: col.id,
        field: col.field,
        name: col.name
      }, {})
    return result;
  }, {})
);

/*
 * Variables
 */
// Sidebar element reference
let sidebar = ref(null);
// Data-table items list
let list = ref([]);
// Decoded URI query
const query = decodeUriQuery(window.location.search)
// Page filters list
let filters = reactive(getFilters(query))
// List of active columns
let columns = ref(activeColumns(page.props.sections))
// Load roles
getList(filters)
</script>