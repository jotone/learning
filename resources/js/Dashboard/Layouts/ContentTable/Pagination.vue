<template>
  <nav class="pagination">
    <ul v-if="options.last_page > 1">
      <li>
        <a
          :href="`${options.path}${buildUri(1)}`" v-if="options.current_page !== 1"
          @click.prevent="changePage"
        >
          &lt;&lt;
        </a>
      </li>
      <li>
        <a
          :href="`${options.path}${buildUri(options.current_page - 1)}`"
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
          <a :href="`${options.path}${buildUri(index)}`">{{ index }}</a>
        </li>
      </template>

      <li>
        <a
          :href="`${options.path}?${buildUri(options.current_page + 1)}`"
          v-if="options.current_page !== options.last_page"
          @click.prevent="changePage"
        >
          &gt;
        </a>
      </li>
      <li>
        <a
          :href="`${options.path}?${buildUri(options.last_page)}`"
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
  name: "Pagination",
  props: ["options", "path"],
  beforeMount() {
    console.log()
  },
  methods: {
    buildUri(page) {
      return this.path.replace(/page=\d/, 'page=' + page)
    },
    changePage(e) {
      const _this = $(e.target).closest('a');
      const obj = new URL(_this.attr('href'));
      const uriParams = JSON.parse('{"' +
        decodeURI(obj.search.substring(1))
          .replace(/"/g, '\\"')
          .replace(/&/g, '","')
          .replace(/=/g, '":"') +
        '"}')

      this.$parent.$parent.$attrs.filters.page = uriParams.page

      this.$parent.$parent.getCollection().then(() => {
        let uri = `?page=${uriParams.page}`;
        if (!!uriParams.search) {
          uri += `&search=${uriParams.search}`
        }
        window.history.pushState(this.$parent.$parent.$parent.initialPage, '', uri);
      })
    }
  }
}
</script>