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
              <CircleProgress :current="form.name?.length || 0" max="60"/>
            </label>
            <button class="btn blue">
              <span>Add</span>
            </button>
          </form>
        </div>

        <div class="sortable-list">
          <CategoriesList :list="list.data"/>
        </div>

        <SliderCheckbox name="cats_inst_courses" text="Use Categories on User Dashboard"/>

        <p class="popup-text-row">
          Note: All courses should belong to a category in order to<br>be able to select this view.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import {DefaultPopupMixin} from '../../../../../mixins/default-popup-mixin.js';
import {CircleProgress, SliderCheckbox} from "../../../../components/Form/index.js";
import CategoriesList from "./CategoriesList.vue";

export default {
  components: {SliderCheckbox, CategoriesList, CircleProgress},
  mixins: [DefaultPopupMixin],
  data() {
    return {
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
      list: []
    }
  },
  methods: {
    /**
     * Send a request to create a category
     */
    categoryStore() {
      const query = `mutation {create (name: "${this.form.name}", type: "course") {id name position}}`
      this.requestGraphQL(this.$page.props.routes.category.api, query)
        .then(response => null !== response?.data?.data?.create && this.list.data.push(response.data.data.create))
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
    }
  },
  beforeMount() {
    // Get list of categories
    const query = `{categories(
       per_page:${this.filters.per_page}, order_by:"${this.filters.order.by}", order_dir:"${this.filters.order.dir}", page:${this.filters.page}
     ) {
      total per_page last_page has_more_pages current_page data {
        id name position
      }
    }}`
    this.requestGraphQL(this.$page.props.routes.category.api, query)
      .then(response => {
        console.log(response.data.data.categories)
        this.list = response.data.data.categories;
      })
  },
  inject: ['requestGraphQL'],
}
</script>