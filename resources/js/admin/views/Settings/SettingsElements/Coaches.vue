<template>
  <SettingsElement name="Coaches">
    <div class="card" style="margin-top: 20px">
      <div class="content-table-wrap">
        <div class="content-table-controls">
          <SearchForm
            placeholder="Search for a Role by the name or slug…"
            :search="filters?.search"
            @runSearch="runSearch"
          />

          <a class="btn" :href="routes.user.create">
            <i class="icon user-plus-icon"></i>
            <span>Create Coach</span>
          </a>
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
              <th>
                <TableHeadCol field="created_at" :filters="filters" name="Creation Date" @changeDirection="changeDirection"/>
              </th>
              <th>
                <TableHeadCol field="last_activity" :filters="filters" name="Last Login" @changeDirection="changeDirection"/>
              </th>
              <th>
                <TableHeadCol name="Actions"/>
              </th>
            </tr>
            </thead>
            <tbody>
            <template v-for="user in list.data">
              <CoachesTableRow :user="user" @action="showRowActions"/>
            </template>
            </tbody>
          </table>
        </div>

        <RowActions
          :actions="rowActions"
          :model="selectedRow.model"
          :show="selectedRow.show"
          :right="selectedRow.right"
          :top="selectedRow.top"
        />

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
  </SettingsElement>
</template>

<script setup lang="ts">
// Vue libs
import {inject, reactive, ref} from 'vue';
// Other Libs
import {decodeUriQuery, encodeUriQuery} from "../../../libs/RequestHelper";
// Interfaces
import {FiltersInterface} from "../../../../contracts/FiltersInterface";
import {UserDataInterface} from '../../../../contracts/UserDataInterface';
// Components
import CoachesTableRow from './CoachesTableRow.vue';
import SettingsElement from './SettingsElement.vue';
import {getFilters, Pagination, PerPage, RowActions, SearchForm, TableHeadCol} from '../../../components/DataTable/index.js';

const props = defineProps({
  roles: {
    type: Array,
    required: true
  },
  routes: {
    type: Object,
    required: true
  }
});

// Get content roles function
const requestGraphQL = inject('requestGraphQL')

/*
 * Methods
 */
/**
 * GraphQL query string to get roles list
 * @param {FiltersInterface} filters
 * @returns {string}
 */
const listQuery = (filters: FiltersInterface): string => `{users(
  role_id:[${props.roles.join(',')}],
  per_page:${filters.per_page},
  order_by:"${filters.order.by}",
  order_dir:"${filters.order.dir}",
  page:${filters.page},
  search:"${filters.search}"
) {
  total per_page last_page has_more_pages current_page data {
    id first_name last_name email img_url last_activity created_at
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

const showRowActions = (e, user: UserDataInterface) => {
  const row = e.target.closest('tr');
  const blockOffset = row.getBoundingClientRect();
  selectedRow.model = user;
  selectedRow.right = 10;
  selectedRow.top = blockOffset.height * (row.rowIndex + 1);
  selectedRow.show = true;
}

// Selected row model ID
let selectedRow = reactive({
  model: {},
  right: 0,
  top: 0,
  show: false
});

// List of actions for the row popup
const rowActions = [
  {
    name: 'Edit',
    icon: 'edit-icon',
    link: props.routes.user.edit
  },
  {
    name: 'Remove',
    icon: 'trash-icon',
    callback: () => {

    }
  }
]

/**
 * Send request to get a role list
 * @param {FiltersInterface} filters
 * @param {null|function} callback
 */
const getList = (filters: FiltersInterface, callback?: Function) => {
  requestGraphQL(props.routes.user.api, listQuery(filters))
    .then(response => {
      list.value = response.data.data.users;
      typeof callback === 'function' && callback(filters)
    })
}
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