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
              <SearchForm/>
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
                <td>
                  <Link :href="userEdit(model)">{{ model.email }}</Link>
                </td>
                <td>
                  <Link :href="userEdit(model)">{{ model.role_name }}</Link>
                </td>
                <td>
                  <Link :href="userEdit(model)">{{ model.created_at }}</Link>
                </td>
                <td>
                  <Link :href="userRemove(model.id)" class="remove"></Link>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
  </Layout>
</template>

<script>
import {ContentTableMixin} from "../../Mixins/content-table-mixin";

export default {
  mixins: [ContentTableMixin],
  name: "Users/Index",
  methods: {
    userEdit(model) {
      let type = 'student' === model.role_slug ? 'students' : 'users';
      return  this.$attrs.routes[type].edit.replace(/0$/, model.id);
    },
    userRemove(id) {
      return this.$attrs.routes.users.destroy.replace(/0$/, id)
    }
  },
  beforeMount() {
    this.url = this.$attrs.routes.users.list
  }
}
</script>