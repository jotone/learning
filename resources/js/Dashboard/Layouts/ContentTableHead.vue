<template>
  <th
    class="toggle-sort"
    :class="{ active: filters.order.by === field }"
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
  data() {
    return {
      filters: {}
    }
  },
  methods: {
    changeOrder(e) {
      const _this = $(e.target).closest('th')
      console.log(_this)
    }
  },
  beforeMount() {
    this.filters = this.$parent.$parent.filters
  }
}
</script>