<template>
  <Layout>
    <template v-slot:optionals>
      <a class="btn" :href="$attrs.routes.roles.create">
        <i class="icon plus-icon"></i>
        <span>Create Role</span>
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
                <ContentTableHead field="name" name="Name"/>
                <ContentTableHead field="level" name="Level"/>
                <ContentTableHead field="created_at" name="Created At"/>
                <th>
                  <span>Actions</span>
                </th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="model in collection">
                <td data-role="name">
                  <Link :href="roleEdit(model.id)">{{ model.name }}</Link>
                </td>
                <td>
                  <Link :href="roleEdit(model.id)">{{ model.level }}</Link>
                </td>
                <td>
                  <Link :href="roleEdit(model.id)">{{ model.created_at }}</Link>
                </td>
                <td>
                  <a :href="roleRemove(model.id)" class="remove" @click.prevent="roleRemoveAction"></a>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <Confirmation
        :text='`Do you really want to remove role "<b>${removalEntity}</b>" with all it&apos;s data and user permissions?`'
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
  name: "Roles/Index",
  mixins: [ContentTableMixin],
  methods: {
    /**
     * Generate role edit url
     * @param id
     * @return {string}
     */
    roleEdit(id) {
      return this.$attrs.routes.roles.edit.replace(/:id$/, id)
    },
    /**
     * Generate role remove url
     * @param id
     * @return {string}
     */
    roleRemove(id) {
      return this.$attrs.routes.roles.destroy.replace(/:id$/, id)
    },
    /**
     * Remove role
     * @param e
     */
    roleRemoveAction(e) {
      this.removeEntity($(e.target).closest('a'), 'Role ":entity" was successfully removed.')
    },
  },
  beforeMount() {
    this.url = this.$attrs.routes.roles.list
  }
}
</script>