<template>
  <div class="overlay" @click="close" v-if="active">
    <div class="default-popup-wrap">
      <div class="close-popup" @click="close"><i class="icon close-icon"></i></div>

      <div class="popup-title-wrap">Categories</div>
    </div>
  </div>
</template>

<script>
import {DefaultPopupMixin} from '../../../../mixins/default-popup-mixin.js';
import {FiltersInterface} from '../../../../contracts/FiltersInterface.js';

export default {
  mixins: [DefaultPopupMixin],
  data() {
    return {
      filters: {
        page: 1,
        per_page: 0,
        order: {
          by: 'position',
          dir: 'asc'
        }
      }
    }
  },
  methods: {
    listQuery(filters) {
      return `{category(
  per_page:${this.filters.per_page},
  order_by:"${this.filters.order.by}",
  order_dir:"${this.filters.order.dir}",
  page:${this.filters.page}
) {
  total per_page last_page has_more_pages current_page data {
    id name url position type
  }
}}`
    },
    /*getList(filters: FiltersInterface, callback?: Function) {
      requestGraphQL(page.props.routes.course.api, listQuery(filters))
        .then(response => {
          list.value = response.data.data.courses;
          typeof callback === 'function' && callback(filters)
        })
    },*/
    open() {
      this.active = true;

      return new Promise(resolve => {
        this.resolver = resolve
      })
    }
  },
  beforeMount() {
    console.log(this.$page.props.routes.category.api, this.requestGraphQL)
    this.requestGraphQL()
  },
  inject: ['requestGraphQL'],
}
</script>