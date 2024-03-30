<template>
  <BulkActions
    ref="bulkActionsElem"
    :total="list.total"
    :selected="bulkActions.selected"
    :options="bulkActions.options"
    :counter="bulkActions.checkedNum"
    @clear="bulkCheckBoxToggleAll"
  />

  <Teleport to="body">
    <BindingPopup
      ref="addToCategoryModal"
      title="Add to Category"
      text="Select the category to add the selected courses:"
      caption="The courses you want to enroll to “:entity” are the following:"
    />

    <BindingPopup
      ref="removeFromCategoryModal"
      title="Remove from Category"
      text="Select the category to remove from the selected courses:"
      caption="The courses you want to remove from “:entity” are the following:"
      :button="{class: 'danger', confirmation: 'Remove', text: 'Remove'}"
    />

    <BindingPopup
      ref="setCourseStatus"
      title="Set Status"
      text="Select the status to set the selected courses:"
      caption="The courses you want to set “:entity” status are the following:"
      :button="{class: 'blue', confirmation: 'Apply', text: 'Apply'}"
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
import {inject, reactive, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
// Interfaces
import {ResponseInterface} from "../../../../contracts/ResponseInterface";
// Components
import {BulkActions} from "../../../components/DataTable";
import {BindingPopup, RemovePopup, SuccessPopup} from "../../../components/Popup";

// Assign the GraphQl request function
const requestGraphQL = inject('requestGraphQL');

// Assign the function to emit
const emit = defineEmits(['clear', 'courseRemoved', 'getList'])

const page = usePage();

const props = defineProps({
  categories: {
    type: [Array, Object],
    required: true
  },
  list: {
    type: Object<ResponseInterface>,
    required: true,
  }
})

const selector = '.table-container table';

/**
 * Select or unselect all bulk action checkboxes
 * @param e
 * @param state
 */
const bulkCheckBoxToggleAll = (e, state?: boolean) => emit('clear', e, state);

/**
 * Retrieve a list of ids of the selected courses
 * @return {Promise}
 */
const getSelectedItems = function () {
  return new Promise(resolve => {
    if (bulkActions.checkedNum === props.list.total) {
      // Remove all. Get all courses data
      requestGraphQL(page.props.routes.course.api, `{courses(per_page:0) {total per_page data { id name categories {id name} }}}`)
        .then(response => resolve(response.data.data.courses.data))
    } else {
      // Remove selected. Get a list of selected courses ids
      const values = Array.from(document.querySelectorAll(`${selector} tbody tr input[name="checkEl"]:checked`))
        .map(input => parseInt(input.value));
      resolve(props.list.data.filter(item => values.includes(item.id)))
    }
  })
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
  const coursesToUpdate = props.list.data.filter(item => items.includes(item.id));
  // Iterate through the course categories and apply callback function to add or remove the category
  for (let i = 0, n = coursesToUpdate.length; i < n; i++) {
    const course = coursesToUpdate[i]

    let courseCategories = callback(course, category);

    // Send a request to update course with categories
    requestGraphQL(
      page.props.routes.course.api,
      `mutation {update (id: ${course.id}, categories: [${courseCategories.join(',')}]) {id}}`
    ).then(response => emit('getList', response))
  }
}

/**
 * Unselect checkboxes
 */
const uncheckSelected = () => {
  document.querySelector(`${selector} thead input[name="checkAll"]`).checked = false;
  const nodes = document.querySelectorAll(`${selector} tbody tr input[name="checkEl"]`);
  for (let i = 0, n = nodes.length; i < n; i++) {
    nodes[i].checked = false
  }
  bulkActionsElem.value.show = false
}


// Bulk actions list
let bulkActions = reactive({
  checkedNum: 0,
  selected: 'Bulk Actions',
  options: [
    {
      value: null,
      disabled: true,
      text: 'Bulk Actions'
    }, {
      value: 'delete',
      text: 'Delete',
      // Remove courses handler. Gets selected courses and send a request to remove them
      callback: () => getSelectedItems().then((courses: Array<object>) => {
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
            Promise.all(requests).then(() => emit('courseRemoved', items))
          }
        })
      })
    }, {
      value: 'export',
      text: 'Export to CSV',
      // Export courses handler. Gets selected courses and send a request to export them to csv file
      callback: () => getSelectedItems().then((courses: Array<object>) => request({
        url: page.props.routes.course.export,
        method: 'post',
        data: {list: courses.map(item => item.id)},
        onSuccess: response => 201 === response.status && successModal.value.open(response.data.admins)
      }))
    }, {
      value: 'addToCat',
      text: 'Add to Category',
      // Open the modal window for adding courses to the category
      callback: () => getSelectedItems().then((courses: Array<object>) => {
        // Build a list of courses [{id: course.id, text: course.name}]
        const items = mapCourses(courses);
        uncheckSelected();

        // Open the category add modal
        addToCategoryModal.value
          .open(items, props.categories)
          .then(result => false !== result && modifyCourseCategories(
            result.items,
            result.option,
            // Add a new category if it does not exist
            (course: object, category: number) => {
              let courseCategories = course.categories.map(item => item.id)
              if (!courseCategories.includes(category)) {
                courseCategories.push(category)
              }
              return courseCategories
            }
          ))
      })
    }, {
      value: 'renameFromCat',
      text: 'Remove from Category',
      // Open the modal window for removing courses from the category
      callback: () => getSelectedItems().then((courses: Array<object>) => {
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
          .then(result => false !== result && modifyCourseCategories(
            result.items,
            result.option,
            // Remove the category from the category list
            (course: object, category: number) => course.categories
              .map(item => item.id)
              .filter(item => item !== category)
          ))
      })
    }, {
      value: 'activate',
      text: 'Set Status',
      callback: () => getSelectedItems().then((courses: Array<object>) => {
        // Build a list of courses [{id: course.id, text: course.name}]
        const items = mapCourses(courses);
        uncheckSelected();

        const statuses = page.props.statuses.map(item => ({
          id: item,
          name: item.ucfirst().replace(/[_-]+/, ' ')
        }))
        setCourseStatus.value.open(items, statuses).then(result => {
          if (false !== result) {
            const requests = [];
            for (let i = 0, n = result.items.length; i < n; i++) {
              requests.push(requestGraphQL(
                page.props.routes.course.api,
                `mutation {update (id: ${result.items[i]}, status: "${result.option}") {id}}`
              ))
            }
            Promise.all(requests).then(response => emit('getList', response))
          }
        })
      })
    }
  ]
})

let bulkActionsElem = ref(null)
// Add courses to the category element reference
let addToCategoryModal = ref(null);
// Remove courses from the category element reference
let removeFromCategoryModal = ref(null);
// RemoveCourseModal element reference
let removeCourseModal = ref(null);
// SetCourseStatusModal element reference
let setCourseStatus = ref(null);
// SuccessModal element reference
let successModal = ref(null);

defineExpose({bulkActions})
</script>