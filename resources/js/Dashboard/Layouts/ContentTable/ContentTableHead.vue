<template>
  <th
    class="toggle-sort"
    :class="{ active: field === $parent.$parent.$attrs.filters.order.by }"
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
  methods: {
    changeOrder(e) {
      const _this = $(e.target).closest('th')
      const field = _this.find('[data-sort]').attr('data-sort')
      const filters = this.$parent.$parent.$attrs.filters
      if (field === filters.order.by) {
        this.$parent.$parent.$attrs.filters.order.dir = filters.order.dir === 'asc' ? 'desc' : 'asc'
      }
      this.$parent.$parent.$attrs.filters.order.by = field

      this.$parent.$parent.getCollection()
    }
  }
}
</script>