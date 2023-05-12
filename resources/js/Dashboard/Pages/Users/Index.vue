<template>
  <Layout>
    <template v-slot:optionals>
      <a class="btn" :href="$attrs.routes.users.create">
        <i class="icon user-add-icon"></i>
        <span>Create User</span>
      </a>
    </template>

    <template v-slot:content>
      <div class="page-content-wrap">
        <Pagination :options="pagination" :url="url"/>

        <div class="table-group">

          <div class="table-optional">
            <div class="table-info"></div>

            <div class="table-filtering">
              <SearchForm placeholder="Search for users…"/>
            </div>
          </div>

          <div class="table-wrap">
            <table>
              <thead>
              <tr>
                <ContentTableHead field="first_name" name="First Name"/>
                <ContentTableHead field="last_name" name="Last Name"/>
                <ContentTableHead field="email" name="Email"/>
                <ContentTableHead field="role_name" name="Role"/>
                <th><span>Image</span></th>
                <ContentTableHead field="created_at" name="Created At"/>
                <th>
                  <span>Actions</span>
                </th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="model in collection">
                <td>
                  <Link :href="userEdit(model)">{{ model.first_name }}</Link>
                </td>
                <td>
                  <Link :href="userEdit(model)">{{ model.last_name }}</Link>
                </td>
                <td data-role="email">
                  <Link :href="userEdit(model)">{{ model.email }}</Link>
                </td>
                <td>
                  <Link :href="userEdit(model)">{{ model.role_name }}</Link>
                </td>
                <td>
                  <Link class="img-container" :href="userEdit(model)">
                    <img
                      v-if="!!model.img_url"
                      :src="model.img_url.small || model.img_url.large || model.img_url.original"
                      alt=""
                    >
                  </Link>
                </td>
                <td>
                  <Link :href="userEdit(model)">{{ model.created_at }}</Link>
                </td>
                <td>
                  <a :href="userRemove(model.id)" class="remove" @click.prevent="userRemoveAction"></a>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <Confirmation
        :text='`Do you really want to remove user "<b>${removalEntity}</b>" with all his data and progress?`'
        ref="confirmation"
        okBtnClass="danger"
        okText="Remove"
        noText="Cancel"
      />
    </template>
  </Layout>
</template>

<script>
import {ContentTableMixin} from "../../Mixins/content-table-mixin";

export default {
  mixins: [ContentTableMixin],
  name: "Users/Index",
  methods: {
    /**
     * Generate user edit url
     * @param model
     * @return {string}
     */
    userEdit(model) {
      let type = 'student' === model.role_slug ? 'students' : 'users';
      return this.$attrs.routes[type].edit.replace(/:id/, model.id);
    },
    /**
     * Generate user remove url
     * @param id
     * @return {string}
     */
    userRemove(id) {
      return this.$attrs.routes.users.destroy.replace(/:id/, id)
    },
    /**
     * Remove user
     * @param e
     */
    userRemoveAction(e) {
      this.removeEntity($(e.target).closest('a'), 'User ":entity" was successfully removed.')
    }
  },
  beforeMount() {
    this.url = this.$attrs.routes.users.list
  },
  mounted() {
    setTimeout(() => {
      let items = {}
      document.querySelectorAll('.table[aria-label] tbody tr').forEach(e=> {
        let nodes = e.childNodes
        if (typeof items[nodes[7].textContent.trim()] === "undefined") {
          items[nodes[7].textContent.trim()] = []
        }
        items[nodes[7].textContent.trim()].push(nodes[9].textContent.trim())
      })
      console.log(JSON.stringify(items))
    }, 100)
  }
}
</script>