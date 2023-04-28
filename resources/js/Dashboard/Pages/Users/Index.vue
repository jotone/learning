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
        :text='`Do you really want to remove user "<b>${userEmail}</b>" with all his data and progress?`'
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
  data() {
    return {
      userEmail: ''
    }
  },
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
      return this.$attrs.routes[type].edit.replace(/0$/, model.id);
    },
    /**
     * Generate user remove url
     * @param id
     * @return {string}
     */
    userRemove(id) {
      return this.$attrs.routes.users.destroy.replace(/0$/, id)
    },

    userRemoveAction(e) {
      const obj = $(e.target).closest('a');
      if (typeof obj.attr('href') !== 'undefined') {
        this.userEmail = obj.closest('tr').find('[data-role]').text().trim()

        this.$refs.confirmation
          .open()
          .then(res => res && $.axios.delete(obj.attr('href'))
            .then(response => 204 === response.status && this.getCollection()))
      }
    },
  },
  beforeMount() {
    this.url = this.$attrs.routes.users.list
  }
}
</script>