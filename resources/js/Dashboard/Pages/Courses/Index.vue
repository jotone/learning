<template>
  <Layout>
    <template v-slot:optionals>
      <a class="btn" :href="$attrs.routes.courses.create">
        <i class="icon course-add-icon"></i>
        <span>Create Course</span>
      </a>
    </template>

    <template v-slot:content>
      <div class="page-content-wrap">
        <div class="page-content-controls-wrap">
          <div class="page-content-controls-left-side">
            <ul class="items-list-style-wrap">
              <li v-for="(type, i) in listStyle.items">
                <i :class="`icon ${type}-icon ${listStyle.active === i ? 'active' : ''}`"></i>
              </li>
            </ul>

            <SearchForm placeholder="Search hor courses..."/>
          </div>

          <Pagination :options="pagination" :url="url"/>
        </div>

        <div class="course-listing">
          <template v-for="(n, i) in collection.length">
            <CourseTableRow :model="collection[i]"/>
          </template>
        </div>
      </div>
    </template>
  </Layout>
</template>

<script>

import {ContentTableMixin} from "../../Mixins/content-table-mixin";
import CourseTableRow from "./Partials/CourseTableRow.vue";

export default {
  components: {CourseTableRow},
  data() {
    return {
      listStyle: {
        active: 0,
        items: [
          'tile',
          'list'
        ]
      }
    }
  },
  mixins: [ContentTableMixin],
  name: "Courses/Index",
  beforeMount() {
    this.url = this.$attrs.routes.courses.list
  }
}
</script>