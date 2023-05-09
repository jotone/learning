<template>
  <Layout>
    <template v-slot:optionals>
      <a class="btn" :href="$attrs.routes.coaches.create">
        <i class="icon plus-icon"></i>
        <span>Create Coach</span>
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
        :text='`Do you really want to remove coach "<b>${removalEntity}</b>"?`'
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
  name: "Coaches/Index",
  mixins: [ContentTableMixin],
  methods: {
    /**
     * Generate user edit url
     * @param model
     * @return {string}
     */
    userEdit(model) {
      return this.$attrs.routes.coaches.edit.replace(/0$/, model.id);
    },
    /**
     * Generate user remove url
     * @param id
     * @return {string}
     */
    userRemove(id) {
      return this.$attrs.routes.coaches.destroy.replace(/0$/, id)
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
    this.url = this.$attrs.routes.coaches.list
  }
}
</script>