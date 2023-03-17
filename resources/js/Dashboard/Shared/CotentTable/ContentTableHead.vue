<template>
  <th
    class="toggle-sort"
    :class="{ active: field === $page.props.filters.order.by }"
    @click.prevent="changeOrder"
  >
    <span>{{ name }}</span>
    <div :data-sort="field"></div>
  </th>
</template>

<script>
export default {
  name: "ContentTableHead",
  props: ["field", "name"],
  inject: ['getCollection'],
  methods: {
    changeOrder(e) {
      const _this = $(e.target).closest('th')
      const field = _this.find('[data-sort]').attr('data-sort')
      const filters = this.$page.props.filters
      if (field === filters.order.by) {
        this.$page.props.filters.order.dir = filters.order.dir === 'asc' ? 'desc' : 'asc'
      }
      this.$page.props.filters.order.by = field
      this.getCollection()
    }
  }
}
</script>