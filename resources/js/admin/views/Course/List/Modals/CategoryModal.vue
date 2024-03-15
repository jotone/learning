<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap category-popup">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">Categories</div>

      <div class="popup-body-wrap">
        <div class="add-category-wrap">
          <div class="add-category-text">
            Add Category
          </div>
          <form class="add-category-controls" @submit.prevent="categoryStore">
            <label class="add-category-input">
              <input class="form-input" maxlength="60" v-model.trim="form.name" required>
              <CircleProgress :current="form.name?.length || 0" max="60" :showRemnant="true"/>
            </label>
            <button class="btn blue">
              <span>Add</span>
            </button>
          </form>
        </div>

        <div class="sortable-list scrollbar">
          <CategoriesList :list="list.data" @change="categorySort" @showControls="showRowActions"/>
        </div>

        <SliderCheckbox
          name="cats_inst_courses"
          text="Use Categories on User Dashboard"
          :checked="categoriesInsteadOfCourses"
          @change="categoriesInsteadOfCoursesChange"
        />

        <p class="popup-text-row">
          Note: All courses should belong to a category in order to<br>be able to select this view.
        </p>

        <RowActions
          :actions="actions"
          :model="selectedCategory.model"
          :show="selectedCategory.show"
          :right="selectedCategory.right"
          :top="selectedCategory.top"
        />
      </div>
    </div>
  </div>

  <teleport to="body">
    <RemovePopup
      title="Are you sure you want to delete this category?"
      ref="removeCategoryModal"
      :listMessages="{
      bottom: ['This will delete all content irrevocably.', 'Type <b>Delete</b> to confirm.']
    }"
    />
  </teleport>
</template>

<script>
// Mixin
import {DefaultPopupMixin} from '../../../../../mixins/default-popup-mixin.js';
// Components
import CategoriesList from "./CategoriesList.vue";
import {CircleProgress, SliderCheckbox} from "../../../../components/Form/index.js";
import {RowActions} from "../../../../components/DataTable/index.js";
import RemovePopup from "../../../../components/Popup/RemovePopup.vue";
import {Notification} from "../../../../libs/Notification.js";

export default {
  components: {RemovePopup, CategoriesList, CircleProgress, RowActions, SliderCheckbox},
  mixins: [DefaultPopupMixin],
  data() {
    return {
      actions: [
        {
          name: 'Edit',
          icon: 'edit-icon',
          link: this.$page.props.routes.category.edit
        }, {
          name: 'Remove',
          icon: 'trash-icon',
          callback: () => {
            const items = [{text: this.selectedCategory.model.name, id: this.selectedCategory.model.id}];
            this.$refs.removeCategoryModal.open(items).then(result => {
              if (false !== result && typeof result === 'object') {
                const requests = [];
                for (let i = 0, n = result.length; i < n; i++) {
                  requests.push(
                    this.requestGraphQL(
                      this.$page.props.routes.category.api,
                      `mutation {destroy(id:${result[i].id}){id}}`
                    )
                  )
                }
                Promise.all(requests).then(() => {
                  this.getCategories();
                  Notification.warning(`Category "${items[0].text}" was successfully removed.`);
                })
              }
            });
          }
        }
      ],
      categoriesInsteadOfCourses: Boolean(+this.$page.props.settings.cats_inst_courses),
      form: {
        name: ''
      },
      filters: {
        page: 1,
        per_page: 0,
        order: {
          by: 'position',
          dir: 'asc'
        }
      },
      list: [],
      selectedCategory: {
        model: {},
        right: 0,
        top: 0,
        show: false
      }
    }
  },
  methods: {
    /**
     * Send request to enable or disable the option "show categories instead of courses on the main page"
     * @param {boolean} val
     */
    categoriesInsteadOfCoursesChange(val) {
      this.categoriesInsteadOfCourses = val;
      this.request({
        url: this.$page.props.routes.settings,
        method: 'patch',
        data: {
          cats_inst_courses: val
        }
      })
    },
    /**
     * Send a request to create a category
     */
    categoryStore() {
      const query = `mutation {create (name: "${this.form.name}", type: "course") {id name position}}`
      this.requestGraphQL(this.$page.props.routes.category.api, query)
        .then(response => null !== response?.data?.data?.create && this.list.data.push(response.data.data.create))
    },
    /**
     * Send the categories sorting request
     */
    categorySort() {
      let ids = this.list.data.map((item, index) => {
        item.position = index;
        return item.id;
      });
      let query = `mutation {sort (list: [${ids.join(',')}]) {id}}`

      this.requestGraphQL(this.$page.props.routes.category.api, query)
    },
    /**
     * Get the list of the categories
     */
    getCategories() {
      // Get list of categories
      const query = `{categories(
         per_page:${this.filters.per_page},
         order_by:"${this.filters.order.by}",
         order_dir:"${this.filters.order.dir}",
         page:${this.filters.page},
         type:"courses"
       ) {
        total per_page last_page has_more_pages current_page data {
          id name position
        }
      }}`
      this.requestGraphQL(this.$page.props.routes.category.api, query)
        .then(response => {
          this.list = response.data.data.categories;
        })
    },
    /**
     * Open the modal window
     * @returns {Promise<unknown>}
     */
    open() {
      this.active = true;

      return new Promise(resolve => {
        this.resolver = resolve
      })
    },
    /**
     * Show category actions
     * @param {Event} e
     * @param {int} i
     */
    showRowActions(e, i) {
      const row = e.target.closest('li');
      const popupOffset = e.target.closest('.category-popup').getBoundingClientRect() //+ e.target.getBoundingClientRect().height;
      const blockOffset = row.getBoundingClientRect();

      this.selectedCategory.model = this.list.data[i];
      this.selectedCategory.right = 20;
      this.selectedCategory.top = (10 + blockOffset.height * (i + 1)) + popupOffset.top + 100;
      this.selectedCategory.show = true;
    }
  },
  beforeMount() {
    this.getCategories()
  },
  inject: ['request', 'requestGraphQL'],
}
</script>