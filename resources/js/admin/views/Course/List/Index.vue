<template>
  <header>
    <div class="page-name-wrap">
      <h1>Courses</h1>

      <a class="btn" href="#" @click.prevent="courseModalShow">
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

      <CourseBulkActions
        ref="courseBulkActions"
        :categories="categories"
        :list="list"
        @clear="bulkCheckBoxToggleAll"
        @courseRemoved="bulkActionsCourseRemoveHandler"
        @getList="refreshDataTable"
      />

      <div class="table-container">
        <table>
          <thead>
          <tr>
            <th class="static">
              <div class="static-fields">
                <div class="bulk-select">
                  <input type="checkbox" name="checkAll" @change="bulkCheckBoxToggleAll">
                </div>
                <TableHeadCol name="Thumbnail"/>
              </div>
            </th>
            <th v-for="column in columns">
              <template v-if="column.field === 'categories'">
                <TableHeadCol
                  :field="column.field"
                  :filters="filters"
                  :name="column.name"
                  :showOrder="false"
                  :showPlusIcon="true"
                  @changeDirection="changeDirection"
                  @plusClick="categoryModalShow"
                />
              </template>

              <template v-else-if="column.field === 'status'">
                <TableHeadCol
                  :field="column.field"
                  :filters="filters"
                  :name="column.name"
                  :showInfoIcon="true"
                  @changeDirection="changeDirection"
                  @hover="showStatusTooltip"
                />
              </template>

              <template v-else>
                <TableHeadCol
                  :field="column.field"
                  :filters="filters"
                  :name="column.name"
                  @changeDirection="changeDirection"
                />
              </template>
            </th>
          </tr>
          </thead>
          <tbody>
          <template v-for="course in list.data">
            <TableRow
              :course="course"
              :columns="columns"
              @changeBulkCheckbox="bulkCheckBoxToggleSingle"
            />
          </template>
          </tbody>
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

  <Teleport to="body">
    <StatusTooltip ref="statusTooltip">
      <div class="status-tooltip-row">
        <b>Active:</b><span>Fully accessible, complete courses available now.</span>
      </div>
      <div class="status-tooltip-row">
        <b>Coming Soon:</b><span>New courses launching soon.</span>
      </div>
      <div class="status-tooltip-row">
        <b>Draft:</b><span>Courses in development, not yet available.</span>
      </div>
    </StatusTooltip>

    <CategoryModal ref="categoryModal" :getCategories="getCategories" @refresh="refreshLists"/>

    <CourseModal ref="courseModal" :statuses="$attrs.statuses"/>
  </Teleport>
</template>

<script setup lang="ts">
// Vue libs
import {inject, reactive, ref} from 'vue';
import {usePage} from '@inertiajs/vue3';
// Other Libs
import {decodeUriQuery, encodeUriQuery} from '../../../libs/RequestHelper';
import {Notification} from "../../../libs/Notification";
// Interfaces
import {ColumnSectionInterface} from '../../../../contracts/ColumnSectionInterface';
import {FiltersInterface} from '../../../../contracts/FiltersInterface';
import {ResponseInterface} from "../../../../contracts/ResponseInterface";
// Components
import CourseBulkActions from "./CourseBulkActions.vue";
import {
  ColumnSelector,
  getFilters,
  Pagination,
  PerPage,
  SearchForm,
  StatusTooltip,
  TableHeadCol
} from '../../../components/DataTable';
import {Notifications, Sidebar} from '../../../components/Default';
import CategoryModal from './Modals/CategoryModal.vue';
import CourseModal from "./Modals/CourseModal.vue";
import TableRow from './TableRow.vue';
// Layout
import Layout from '../../../shared/Layout.vue';

defineOptions({layout: Layout})

// Assign the http request function
const request = inject('request');
// Assign the GraphQl request function
const requestGraphQL = inject('requestGraphQL');

// Page variables
const page = usePage()

/*
 * --------------- Content table ---------------
 */
// Decoded URI query
const query = decodeUriQuery(window.location.search)
query.order = {
  by: 'position',
  dir: 'desc'
}
// Data-table items list
let list = ref([]);
// Page filters list
let filters = reactive(getFilters(query))

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
    id name url img_url instructor_id instructor_email lang status optional_duration position certificate_enable users_count created_at updated_at categories {id name}
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
  window.history.pushState(window.location.origin + window.location.pathname, '', '?' + encodeUriQuery(state))
})

/**
 * Send request to get a role list
 * @param {FiltersInterface} filters
 * @param {null|function} callback
 */
const getList = (filters: FiltersInterface, callback?: Function) =>
  requestGraphQL(page.props.routes.course.api, listQuery(filters))
    .then(response => {
      list.value = response.data.data.courses;
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

const refreshLists = (categoryList: ResponseInterface) => {
  categories.value = categoryList.data;
  getList(filters)
}

/*
 * --------------- Categories list ---------------
 */

let categories = ref([])

/**
 * Get the list of the categories
 */
const getCategories = () => new Promise(resolve => {
  // Get a list of categories
  const query = `{categories(per_page:0,  order_by:"position", order_dir:"asc", page:1, type:"courses") {
    total per_page last_page has_more_pages current_page data {
      id name position
    }
  }}`
  requestGraphQL(page.props.routes.category.api, query).then(response => resolve(response.data.data.categories))
})

// Load categories
getCategories().then((categoryList: ResponseInterface) => {
  categories.value = categoryList.data;
})
/*
 * --------------- Bulk Actions list ---------------
 */

const courseBulkActions = ref(null)

/**
 * Notify that remove courses action is completed
 * @param items
 */
const bulkActionsCourseRemoveHandler = (items: Array<object>) => {
  getList(filters)
  if (items.length > 1) {
    const courses = items.reduce((sum, item, i) => i === 0 ? `${item.text}` : `"${sum}", "${item.text}"`, '')
    Notification.warning(`Courses ${courses} were successfully removed.`);
  } else {
    Notification.warning(`Course "${items[0].text}" was successfully removed.`);
  }
}

/**
 * Select or unselect all bulk action checkboxes
 * @param e
 * @param state
 */
const bulkCheckBoxToggleAll = (e, state = null) => {
  // Set checkbox status. If a state is null then depend on checkbox status, else - force a status
  const status = null === state ? e.target.checked : state;
  // Get element parent table
  const parent = (e?.constructor?.name === 'Event' ? e.target : e).closest('table');
  // Get checkboxes nodes
  const nodes = parent.querySelectorAll('input[name="checkEl"]');
  // Set a number of the selected elements
  courseBulkActions.value.bulkActions.checkedNum = status ? nodes.length : 0;
  // Set status to the checkboxes
  for (let i = 0, n = nodes.length; i < n; i++) {
    nodes[i].checked = status;
  }
  // If status is force stated, change the main checkbox value
  if (null !== state) {
    parent.querySelector('input[name="checkAll"]').checked = state
  }
}

/**
 * Select or unselect the main bulk action checkbox
 * @param e
 */
const bulkCheckBoxToggleSingle = (e: Event) => {
  const parent = e.target.closest('tbody');
  courseBulkActions.value.bulkActions.checkedNum = parent.querySelectorAll('input[name="checkEl"]:checked').length;
  parent.closest('table').querySelector('input[name="checkAll"]').checked = (
    0 !== courseBulkActions.value.bulkActions.checkedNum
    && parent.querySelectorAll('input[name="checkEl"]').length === courseBulkActions.value.bulkActions.checkedNum
  );
}

/**
 * Reload the content table list
 * @param response
 */
const refreshDataTable = response => {
  const data = Array.isArray(response) ? response[0].data : response.data
  data.hasOwnProperty('data') && getList(filters)
};

/*
 * --------------- Column Selector Sidebar ---------------
 */

// Sidebar element reference
let sidebar = ref(null);

/**
 * Show or hide sidebar
 * @param value
 */
const toggleSidebar = (value: boolean = true) => {
  sidebar.value.toggleShow(value);
}

/*
 * --------------- Column Selector Functionality ---------------
 */

/**
 * Get enabled columns list
 * @param sections
 */
let activeColumnsList = (sections: Array<ColumnSectionInterface>) => Object.values( // Reset the result array indexes
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

  columns.value = activeColumnsList(page.props.sections)
}
// List of active columns
let columns = ref(activeColumnsList(page.props.sections))

/*
 * --------------- Status Tooltip ---------------
 */
// StatusTooltip element reference
let statusTooltip = ref(null);

/**
 * View Course status tooltip
 * @param e
 * @param status
 */
const showStatusTooltip = (e: Event, status) => {
  const borders = e.target.closest('.info-icon-wrap').getBoundingClientRect();
  statusTooltip.value.toggleShow(status, {
    left: borders.left + 30,
    top: window.innerWidth > 1200 ? -18 : 47
  })
}

/*
 * --------------- Category modal ---------------
 */
// Category modal reference
let categoryModal = ref(null);

/**
 * Open the category modal window
 */
const categoryModalShow = () => categoryModal.value.open(categories)

/*
 * --------------- Course modal ---------------
 */
// Course modal reference
let courseModal = ref(null);

/**
 * Open the course modal window
 */
const courseModalShow = () => courseModal.value.open().then(result => result && getList(filters))

// Load courses
getList(filters);
</script>