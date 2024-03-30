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

      <BulkActions
        :ref="bulkActionsRef"
        :total="list.total"
        :selected="bulkActions.selected"
        :options="bulkActions.options"
        :counter="bulkActions.checkedNum"
        @clear="bulkCheckBoxToggleAll"
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

    <BindingPopup
      ref="addToCategoryModal"
      title="Add to Category"
      caption="The courses you want to enroll to “:entity” are the following:"
    />

    <BindingPopup
      ref="removeFromCategoryModal"
      title="Remove from Category"
      caption="The courses you want to remove from “:entity” are the following:"
      :button="{class: 'danger', confirmation: 'Remove', text: 'Remove'}"
    />

    <RemovePopup
      ref="removeCourseModal"
      title="Are you sure you want to delete these courses?"
      caption="The courses you want to remove are the following:"
      :listMessages="{bottom: ['This will delete all content irrevocably.', 'Type <b>Delete</b> to confirm.']}"
    />

    <SuccessPopup
      title="Export Courses to CSV"
      caption="We are processing your CSV file. We will send it shortly to the below email addresses:"
      ref="successModal"
    />
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
import {
  BulkActions,
  ColumnSelector,
  getFilters,
  Pagination,
  PerPage,
  SearchForm,
  StatusTooltip,
  TableHeadCol
} from '../../../components/DataTable';
import {BindingPopup, RemovePopup, SuccessPopup} from "../../../components/Popup";
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
  categories = categoryList.data;
  getList(filters)
}

/*
 * --------------- Categories list ---------------
 */

let categories = reactive({})

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

/*
 * --------------- Bulk Actions list ---------------
 */
/**
 * Retrieve a list of ids of the selected courses
 * @return {Promise}
 */
const getSelectedItems = () => new Promise(resolve => {
  if (bulkActions.checkedNum === list.value.total) {
    // Remove all. Get all courses data
    requestGraphQL(page.props.routes.course.api, `{courses(per_page:0) {total per_page data { id name categories {id name} }}}`)
      .then(response => resolve(response.data.data.courses.data))
  } else {
    // Remove selected. Get a list of selected courses ids
    const values = Array
      .from(document.querySelectorAll('.table-container table tbody tr input[name="checkEl"]:checked'))
      .map(input => parseInt(input.value));
    resolve(list.value.data.filter(item => values.includes(item.id)))
  }
})

/**
 * Unselect checkboxes
 */
const uncheckSelected = () => {
  document.querySelector('.table-container table thead input[name="checkAll"]').checked = false;
  const nodes = document.querySelectorAll('.table-container table tbody tr input[name="checkEl"]');
  for (let i = 0, n = nodes.length; i < n; i++) {
    nodes[i].checked = false
  }
}

/**
 * Convert the course array into [{id: course.id, text: course.name}]
 * @param {Array} courses
 */
const mapCourses = (courses: Array<object>) => courses.map(({id, name}) => ({id, text: name}))

/**
 * Get the course category list and modify it
 * @param items
 * @param category
 * @param callback
 */
const modifyCourseCategories = (items: Array<number>, category: number, callback) => {
  // Form a list of courses that should be updated
  const coursesToUpdate = list.value.data.filter(item => items.includes(item.id));
  // Iterate through the course categories and apply callback function to add or remove the category
  for (let i = 0, n = coursesToUpdate.length; i < n; i++) {
    const course = coursesToUpdate[i]

    let courseCategories = callback(course, category);

    // Send a request to update course with categories
    requestGraphQL(
      page.props.routes.course.api,
      `mutation {update (id: ${course.id}, categories: [${courseCategories.join(',')}]) {id}}`
    ).then(response => response.data.hasOwnProperty('data') && getList(filters))
  }
}

/**
 * Open the modal window for adding courses to the category
 */
const courseAddToCategory = () => getSelectedItems().then((courses: Array<object>) => {
  // Build a list of courses [{id: course.id, text: course.name}]
  const items = mapCourses(courses);
  uncheckSelected();

  // Open the category add modal
  addToCategoryModal.value
    .open(items, categories)
    .then(
      result => modifyCourseCategories(
        result.items,
        result.category,
        // Add a new category if it does not exist
        (course, category) => {
          let courseCategories = course.categories.map(item => item.id)
          if (!courseCategories.includes(category)) {
            courseCategories.push(category)
          }
          return courseCategories
        }
      )
    )
})

/**
 * Open the modal window for removing courses from the category
 */
const courseRemoveFromCategory = () => getSelectedItems().then((courses: Array<object>) => {
  const categories = Object.values(courses.reduce((acc, course) => ({
    ...acc,
    ...course.categories.reduce((catAcc, category) => ({...catAcc, [category.id]: category}), {})
  }), {}));

  // Build a list of courses [{id: course.id, text: course.name}]
  const items = mapCourses(courses);
  uncheckSelected();

  // Open the category add modal
  removeFromCategoryModal.value
    .open(items, categories)
    .then(
      result => modifyCourseCategories(
        result.items,
        result.category,
        // Remove the category from the category list
        (course, category) => course.categories.map(item => item.id).filter(item => item !== category)
      )
    )
})

/**
 * Export courses handler. Gets selected courses and send a request to export them to csv file
 */
const courseExportHandler = () => getSelectedItems().then((courses: Array<object>) => request({
  url: page.props.routes.course.export,
  method: 'post',
  data: {list: courses.map(item => item.id)},
  onSuccess: response => 201 === response.status && successModal.value.open(response.data.admins)
}))

/**
 * Remove courses handler. Gets selected courses and send a request to remove them
 */
const courseRemoveHandler = () => getSelectedItems().then((courses: Array<object>) => {
  // Build a list of courses [{id: course.id, text: course.name}]
  const items = mapCourses(courses);
  uncheckSelected();

  // Open the courses remove modal
  removeCourseModal.value.open(items).then(result => {
    if (false !== result && typeof result === 'object') {
      const requests = [];
      // Send requests to remove courses
      for (let i = 0, n = result.length; i < n; i++) {
        requests.push(requestGraphQL(page.props.routes.course.api, `mutation {destroy(id:${result[i].id}){id}}`))
      }

      // Wait for request finish
      Promise.all(requests).then(() => {
        getList(filters)
        if (items.length > 1) {
          const courses = items.reduce((sum, item, i) => i === 0 ? `${item.text}` : `"${sum}", "${item.text}"`, '')
          Notification.warning(`Courses ${courses} were successfully removed.`);
        } else {
          Notification.warning(`Course "${items[0].text}" was successfully removed.`);
        }
        uncheckSelected()
      })
    }
  })
})


// Bulk actions element reference
const bulkActionsRef = ref(null);
// Add courses to the category element reference
let addToCategoryModal = ref(null);
// Remove courses from the category element reference
let removeFromCategoryModal = ref(null);
// RemoveCourseModal element reference
let removeCourseModal = ref(null);
// SuccessModal element reference
let successModal = ref(null);
// Show bulk actions marker
let bulkActions = reactive({
  checkedNum: 0,
  selected: 'Bulk Actions',
  options: [
    {value: null, disabled: true, text: 'Bulk Actions'},
    {value: 'delete', text: 'Delete', callback: courseRemoveHandler},
    {value: 'export', text: 'Export to CSV', callback: courseExportHandler},
    {value: 'addToCat', text: 'Add to Category', callback: courseAddToCategory},
    {value: 'renameFromCat', text: 'Remove from Category', callback: courseRemoveFromCategory},
    {value: 'activate', text: 'Set to Active'}
  ]
})
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
  bulkActions.checkedNum = status ? nodes.length : 0;
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
  bulkActions.checkedNum = parent.querySelectorAll('input[name="checkEl"]:checked').length;
  parent.closest('table').querySelector('input[name="checkAll"]').checked = (
    0 !== bulkActions.checkedNum
    && parent.querySelectorAll('input[name="checkEl"]').length === bulkActions.checkedNum
  );
}

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
// Load categories
getCategories().then((categoryList: ResponseInterface) => {
  categories = categoryList.data;
})
</script>