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
          <CategoryList :list="list" @change="categorySort" @showControls="showRowActions"/>
        </div>

        <SliderCheckbox
          name="cats_inst_courses"
          text="Use Categories on User Dashboard"
          :checked="categoriesInsteadOfCourses"
          @change="categoriesInsteadOfCourseChange"
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
// Other Libs
import {Notification} from "../../../../libs/Notification.js";
// Components
import CategoryList from "./CategoryList.vue";
import {CircleProgress, SliderCheckbox} from "../../../../components/Form/index.js";
import {RowActions} from "../../../../components/DataTable/index.js";
import RemovePopup from "../../../../components/Popup/RemovePopup.vue";
export default {
  components: {RemovePopup, CategoryList, CircleProgress, RowActions, SliderCheckbox},
  mixins: [DefaultPopupMixin],
  emits: ['refresh'],
  props: {
    getCategories: {
      type: Function,
      required: true
    }
  },
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
                Promise.all(requests).then(() => this.getCategories().then(categoryList => {
                  this.list = categoryList.data
                  this.$emit('refresh', categoryList)
                  Notification.warning(`Category "${items[0].text}" was successfully removed.`);
                }))
              }
            });
          }
        }
      ],
      categoriesInsteadOfCourses: Boolean(+this.$page.props.settings.cats_inst_courses),
      form: {
        name: ''
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
    categoriesInsteadOfCourseChange(val) {
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
        .then(response => null !== response?.data?.data?.create && this.list.push(response.data.data.create))
    },
    /**
     * Send the categories sorting request
     */
    categorySort() {
      let ids = this.list.map((item, index) => {
        item.position = index;
        return item.id;
      });
      let query = `mutation {sort (list: [${ids.join(',')}]) {id}}`

      this.requestGraphQL(this.$page.props.routes.category.api, query)
    },

    /**
     * Open the modal window
     * @returns {Promise}
     */
    open(list) {
      this.active = true;
      this.list = list;
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
      const boxShift = 20;
      this.selectedCategory.model = this.list[i];
      this.selectedCategory.right = boxShift;
      this.selectedCategory.top = e.clientY - boxShift;
      this.selectedCategory.show = true;
    }
  },
  inject: ['request', 'requestGraphQL'],
}
</script>