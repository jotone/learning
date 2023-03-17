<template>
  <nav class="pagination">
    <ul v-if="options.last_page > 1">
      <li>
        <a
          :href="`${options.path}${buildUrlParams(1)}`" v-if="options.current_page !== 1"
          @click.prevent="changePage"
        >
          &lt;&lt;
        </a>
      </li>
      <li>
        <a
          :href="`${options.path}${buildUrlParams(options.current_page - 1)}`"
          v-if="options.current_page !== 1"
          @click.prevent="changePage"
        >&lt;</a>
      </li>

      <template v-for="index in options.finish">
        <li
          :class="{active: (options.current_page === index)}"
          v-if="index >= options.start"
          @click.prevent="changePage"
        >
          <a :href="`${options.path}${buildUrlParams(index)}`">{{ index }}</a>
        </li>
      </template>

      <li>
        <a
          :href="`${options.path}?${buildUrlParams(options.current_page + 1)}`"
          v-if="options.current_page !== options.last_page"
          @click.prevent="changePage"
        >
          &gt;
        </a>
      </li>
      <li>
        <a
          :href="`${options.path}?${buildUrlParams(options.last_page)}`"
          v-if="options.current_page !== options.last_page"
          @click.prevent="changePage"
        >
          &gt;&gt;
        </a>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  inject: ["filtersToUri", "getCollection"],
  name: "Pagination",
  props: ["options", "url"],
  methods: {
    /**
     * Build url parameters
     *
     * @param page
     * @returns {*}
     */
    buildUrlParams(page) {
      return this.filtersToUri().replace(/page=\d+/, 'page=' + page)
    },
    /**
     * Click pagination page link
     * @param e
     */
    changePage(e) {
      // Target url
      const _this = $(e.target).closest('a');
      // Get url parameters
      const urlParams = Object.fromEntries(
        (new URLSearchParams(
          (new URL(_this.attr('href'))).search)
        ).entries()
      )
      // Set next page number to the filters variable
      this.$page.props.filters.page = urlParams.page
      // Get next page content
      this.getCollection().then(() => {
        // Set next page url parameters
        let uri = `?page=${urlParams.page}`;
        if (!!urlParams.search) {
          uri += `&search=${urlParams.search}`
        }
        // Set page's new state
        window.history.pushState(this.$parent.$parent.$parent.initialPage, '', uri);
      })
    }
  }
}
</script>