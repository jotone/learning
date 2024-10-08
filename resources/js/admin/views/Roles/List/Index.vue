<template>
  <header>
    <div class="page-name-wrap">
      <h1>Roles</h1>

      <a class="btn" :href="$attrs.routes.create">
        <i class="icon plus-icon"></i>
        <span>Create Role</span>
      </a>
    </div>
  </header>

  <Notifications/>

  <div class="card">
    <div class="content-table-wrap">
      <div class="content-table-controls">
        <SearchForm
          placeholder="Search for a Role by the name or slug…"
          :search="$attrs.filters?.search"
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

  <RemovePopup
    title="Are you sure you want to delete this Role?"
    ref="removeRoleModal"
    :listMessages="{
      bottom: ['This will delete all content irrevocably.', 'Type <b>Delete</b> to confirm.']
    }"
  />
</template>

<script setup lang="ts">
// Vue libs
import {inject, reactive, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
// Other Libs
import {decodeUriQuery, encodeUriQuery} from "../../../libs/RequestHelper";
import {Notification} from "../../../libs/Notification";
// Interfaces
import {FiltersInterface} from "../../../../contracts/FiltersInterface";
import {RoleInterface} from "../../../../contracts/RoleInterface";
// Components
import {getFilters, Pagination, PerPage, RowActions, SearchForm, TableHeadCol} from '../../../components/DataTable';
import Notifications from "../../../components/Default/Notifications.vue";
import RemovePopup from "../../../components/Popup/RemovePopup.vue";
import TableRow from "./TableRow.vue";
// Layout
import Layout from "../../../shared/Layout.vue";

defineOptions({layout: Layout})

// Assign the GraphQl request function
const requestGraphQL = inject('requestGraphQL')

// Page variable
const page = usePage()

/*
 * --------------- Content table ---------------
 */
// Decoded URI query
const query = decodeUriQuery(window.location.search);
// Data-table items list
let list = ref([]);
// Page filters list
let filters = reactive(getFilters(query));

/**
 * GraphQL query string to get roles list
 * @param {FiltersInterface} filters
 * @returns {string}
 */
const listQuery = (filters: FiltersInterface): string => `{roles(
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
      list.value = response.data.data.roles;
      typeof callback === 'function' && callback(filters)
    })

/**
 * Search item results
 * @param {string} search
 */
const runSearch = (search: string) => {
  filters.search = search;
  getList(filters)
}

/*
 * --------------- Actions list ---------------
 */
// Modal for the role remove
const removeRoleModal = ref(null);
// Selected row model ID
let selectedRow = reactive({
  model: {},
  right: 0,
  top: 0,
  show: false
});


/**
 * View row actions in a tooltip panel
 * @param e
 * @param role
 */
const showRowActions = (e, role: RoleInterface) => {
  const row = e.target.closest('tr');
  const blockOffset = row.querySelector('td:last-child').getBoundingClientRect();
  selectedRow.model = role;
  selectedRow.right = blockOffset.width - 160;
  selectedRow.top = blockOffset.height * (row.rowIndex + 1);
  selectedRow.show = true;
}
// List of the row actions
const rowActions = [
  {
    name: 'Edit',
    icon: 'edit-icon',
    link: page.props.routes.edit
  },
  {
    name: 'Remove',
    icon: 'trash-icon',
    callback: () => {
      const items = [{text: selectedRow.model.name, id: selectedRow.model.id}];

      removeRoleModal.value.open(items).then(result => {
        if (false !== result && typeof result === 'object') {
          const requests = [];
          // Send requests to remove roles
          for (let i = 0, n = result.length; i < n; i++) {
            requests.push(requestGraphQL(page.props.routes.api, `mutation {destroy(id:${result[i].id}){id}}`));
          }
          // Wait for requests finish
          Promise.all(requests).then(() => {
            getList(filters)
            if (items.length > 1) {
              let roles = items.reduce((sum, item, i) => i === 0 ? `${item.text}` : `"${sum}", "${item.text}"`, '')
              Notification.warning(`Roles ${roles} were successfully removed.`);
            } else {
              Notification.warning(`Role "${items[0].text}" was successfully removed.`);
            }
          })
        }
      })
    }
  }
]

// Load roles
getList(filters)
</script>