<template>
  <th
    class="toggle-sort"
    :class="{ active: $page.props.hasOwnProperty('filters') && field === $page.props.filters.order.by }"
    @click.prevent="changeOrder"
  >
    <span>{{ name }}</span>
    <div v-if="null !== field" :data-sort="field"></div>
  </th>
</template>

<script>
export default {
  name: "ContentTableHead",
  props: {
    field: {
      type: String,
      default: null
    },
    name: {
      type: String,
      required: true
    }
  },
  inject: ['getCollection'],
  methods: {
    changeOrder(e) {
      if (null !== this.field) {
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
}
</script>