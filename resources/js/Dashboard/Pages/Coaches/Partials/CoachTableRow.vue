<template>
  <tr>
    <td>
      <Link :href="editUrl">{{ model.first_name }}</Link>
    </td>
    <td>
      <Link :href="editUrl">{{ model.last_name }}</Link>
    </td>
    <td>
      <Link :href="editUrl">{{ model.email }}</Link>
    </td>
    <td>
      <Link :href="editUrl">{{ model.created_at }}</Link>
    </td>
    <td>
      <a :href="removeUrl" class="remove" @click.prevent="remove"></a>
    </td>
  </tr>
</template>

<script>

import {defineComponent} from "vue";
import {Link} from "@inertiajs/vue3";

export default defineComponent({
  components: {Link},
  computed: {
    /**
     * Generate coach edit url
     * @return {string}
     */
    editUrl() {
      return this.$page.props.routes.coaches.edit.replace(/:id$/, this.model.id)
    },

    /**
     * Generate coach remove url
     * @return {string}
     */
    removeUrl() {
      return this.$page.props.routes.coaches.destroy.replace(/:id$/, this.model.id)
    },
  },
  methods: {
    /**
     * Remove coach
     * @param e
     */
    remove(e) {
      const obj = $(e.target).closest('a')
      // Set removal object name
      this.$parent.$parent.removalName = this.model.email
      this.$parent.$parent.remove(obj, 'coach.msg.removed')
    },
  },
  name: "CoachTableRow",
  props: ["model"]
})
</script>