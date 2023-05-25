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
      <Link :href="editUrl">{{ model.role_name }}</Link>
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
     * Generate user edit url
     * @return {string}
     */
    editUrl() {
      let type = 'student' === this.model.role_slug ? 'students' : 'users';
      return this.$page.props.routes[type].edit.replace(/:id/, this.model.id);
    },
    /**
     * Generate user remove url
     * @return {string}
     */
    removeUrl() {
      return this.$page.props.routes.users.destroy.replace(/:id/, this.model.id)
    }
  },
  methods: {
    /**
     * Remove user
     * @param e
     */
    remove(e) {
      const obj = $(e.target).closest('a')
      // Set removal object name
      this.$parent.$parent.removalName = this.model.email
      this.$parent.$parent.remove(obj, 'user.msg.removed')
    },
  },
  name: "UserTableRow",
  props: ["model"]
})
</script>