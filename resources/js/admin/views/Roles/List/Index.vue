<template>
  <header>
    <div class="page-name-wrap">
      <h1>{{ page.props.pageName }}</h1>

      <a class="btn">
        <i class="icon plus-icon"></i>
        <span>Role Create</span>
      </a>
    </div>
  </header>

  <div class="card">
    <div class="content-table-wrap">
      <div class="content-table-controls">
        <SearchForm
          placeholder="Search for a Role by the name or slug…"
          :search="$page.props.filters?.search"
          @runSearch="runSearch"
        />
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
              <TableHeadCol field="level" :filters="filters" name="Level" @changeDirection="changeDirection"/>
            </th>
            <th>
              <TableHeadCol field="created_at" :filters="filters" name="Creation Date" @changeDirection="changeDirection"/>
            </th>
            <th>
              <TableHeadCol name="Actions"/>
            </th>
          </tr>
          </thead>
          <tbody>
          <template v-for="role in list.data">
            <TableRow :role="role" @action="showRowActions"/>
          </template>
          </tbody>
        </table>

        <RowActions
          :actions="rowActions"
          :model="selectedRow.model"
          :show="selectedRow.show"
          :right="selectedRow.right"
          :top="selectedRow.top"/>
      </div>

      <div class="page-controls-wrap">
        <PerPage :value="filters.per_page" @changeLimit="changeLimit"/>
        <Pagination
          v-if="list.last_page > 1"
          :buildQuery="buildQuery"
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

  <RemovePopup
    title="Are you sure you want to delete this Role?"
    ref="removeRoleModal"
    :listMessages="{
      bottom: ['This will delete all content irrevocably.', 'Type <b>Delete</b> to confirm.']
    }"
  />
</template>

<script setup lang="ts">
import {inject, reactive, ref} from "vue";
import {decodeUriQuery, encodeUriQuery} from "../../../libs/RequestHelper"
import {usePage} from "@inertiajs/vue3";

import DataTableLayout from "../../../shared/DataTableLayout.vue";
import TableRow from "./TableRow.vue";
import {FiltersInterface} from "../../../../contracts/FiltersInterface";
import {getFilters, Pagination, PerPage, RowActions, SearchForm, TableHeadCol} from '../../../components/DataTables';
import {RoleInterface} from "../../../../contracts/RoleInterface";
import RemovePopup from "../../../components/Popup/RemovePopup.vue";

defineOptions({layout: DataTableLayout})

// Get content roles function
const getList = inject('getList')

const page = usePage()

// Data-table items list
let list = ref([]);
// Remove role modal
const removeRoleModal = ref(null)

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
    link: page.props.routes.roles.edit
  }, {
    name: 'Remove',
    icon: 'trash-icon',
    callback: () => {
      removeRoleModal.value.open([{
        text: selectedRow.model.name,
        id: selectedRow.model.id
      }]).then(result => {
        if (false !== result && typeof result === 'object') {
          console.log(result)
        }
      })
    }
  }
]

// Decoded URI query
const query = decodeUriQuery(window.location.search)

// Page filters list
let filters = reactive(getFilters(query))

/**
 * GraphQL query string to get roles list
 * @param {FiltersInterface} filters
 * @returns {string}
 */
const buildQuery = (filters: FiltersInterface): string => `{roles(
  per_page:${filters.per_page},
  order_by:"${filters.order.by}",
  order_dir:"${filters.order.dir}",
  page:${filters.page},
  search:"${filters.search}"
) {
  total per_page last_page has_more_pages current_page data {
    id name level created_at
  }
}}`

/**
 * Change order
 * @param {any} order
 */
const changeDirection = (order: any) => {
  filters.order = order;
  request(filters)
}

/**
 * Change per page items number
 * @param {number} limit
 */
const changeLimit = (limit: number) => {
  filters.per_page = limit
  request(filters)
}

/**
 * Search item results
 * @param {string} search
 */
const runSearch = (search: string) => {
  filters.search = search;
  request(filters)
}

/**
 * Click pagination element
 * @param {FiltersInterface} filters
 */
const changePage = (filters: FiltersInterface) => request(filters, (filters: FiltersInterface) => {
  let state = {page: filters.page}
  if (filters.search.length) {
    state.search = filters.search
  }
  // Change uri state
  window.history.pushState(window.location.origin + window.location.pathname, "", '?' + encodeUriQuery(state))
})

const showRowActions = (e, role: RoleInterface) => {
  const row = e.target.closest('tr');
  const blockOffset = row.getBoundingClientRect();
  selectedRow.model = role;
  selectedRow.right = 10;
  selectedRow.top = blockOffset.height * row.rowIndex;
  selectedRow.show = true;
}

/**
 * Send request to get roles list
 * @param {FiltersInterface} filters
 * @param {null|function} callback
 */
const request = (filters: FiltersInterface, callback?: Function) =>
  getList(page.props.routes.roles.api, buildQuery(filters))
    .then(response => {
      list.value = response.data.data.roles;
      typeof callback === 'function' && callback(filters)
    })

// Load roles
request(filters)

</script>