<template>
  <tr>
    <td>
      <Link :href="userEdit()">{{ model.first_name }}</Link>
    </td>
    <td>
      <Link :href="userEdit()">{{ model.last_name }}</Link>
    </td>
    <td data-role="email">
      <Link :href="userEdit()">{{ model.email }}</Link>
    </td>
    <td>
      <Link :href="userEdit()">{{ model.role_name }}</Link>
    </td>
    <td>
      <Link class="img-container" :href="userEdit()">
        <img
          v-if="!!model.img_url"
          :src="model.img_url.small || model.img_url.large || model.img_url.original"
          alt=""
        >
      </Link>
    </td>
    <td>
      <Link :href="userEdit()">{{ model.created_at }}</Link>
    </td>
    <td>
      <a :href="userRemove()" class="remove" @click.prevent="userRemoveAction"></a>
    </td>
  </tr>
</template>

<script>

import {defineComponent} from "vue";
import {Link} from "@inertiajs/vue3";

export default defineComponent({
  components: {Link},
  methods: {
    /**
     * Generate user edit url
     * @return {string}
     */
    userEdit() {
      let type = 'student' === this.model.role_slug ? 'students' : 'users';
      return this.$page.props.routes[type].edit.replace(/:id/, this.model.id);
    },
    /**
     * Remove user
     * @param e
     */
    userRemoveAction(e) {
      const obj = $(e.target).closest('a')
      // Set removal object name
      this.$parent.$parent.removalName = this.model.email
      this.$parent.$parent.remove(obj, 'user.remove.success')
    },
    /**
     * Generate user remove url
     * @param id
     * @return {string}
     */
    userRemove() {
      return this.$page.props.routes.users.destroy.replace(/:id/, this.model.id)
    }
  },
  name: "UserTableRow",
  props: ["model"]
})
</script>