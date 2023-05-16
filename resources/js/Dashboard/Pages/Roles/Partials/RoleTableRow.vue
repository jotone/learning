<template>
  <tr>
    <td data-role="name">
      <Link :href="editUrl">{{ model.name }}</Link>
    </td>
    <td>
      <Link :href="editUrl">{{ model.level }}</Link>
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
import {Link} from "@inertiajs/vue3";

export default {
  components: {Link},
  computed: {
    /**
     * Generate role edit url
     * @return {string}
     */
    editUrl() {
      return this.$page.props.routes.roles.edit.replace(/:id$/, this.model.id)
    },
    /**
     * Generate role remove url
     * @return {string}
     */
    removeUrl() {
      return this.$page.props.routes.roles.destroy.replace(/:id$/, this.model.id)
    },
  },
  methods: {
    /**
     * Remove role
     * @param e
     */
    remove(e) {
      const obj = $(e.target).closest('a')
      // Set removal object name
      this.$parent.$parent.removalName = this.model.name
      this.$parent.$parent.remove(obj, 'role.remove.success')
    },
  },
  name: "RoleTableRow",
  props: ["model"]
}
</script>