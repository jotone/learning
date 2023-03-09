<template>
  <DefaultLayout :menu="$attrs.menu" :routes="$attrs.routes" v-slot:content>
    <TopMenu :menu="$attrs.top_menu"/>

    <div class="page-content-wrap">
      <Pagination :options="pagination" :path="filtersToUri()"/>

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
              <td>
                <Link :href="roleEdit(model.id)">{{ model.name }}</Link>
              </td>
              <td>
                <Link :href="roleEdit(model.id)">{{ model.level }}</Link>
              </td>
              <td>
                <Link :href="roleEdit(model.id)">{{ model.created_at }}</Link>
              </td>
              <td>
                <Link :href="roleRemove(model.id)" class="remove"></Link>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script>

import {ContentTableMixin} from "../../Mixins/content-table-mixin";

export default {
  name: "Roles/Index",
  methods: {
    roleEdit(id) {
      return this.$attrs.routes.roles.edit.replace(/0$/, id)
    },
    roleRemove(id) {
      return this.$attrs.routes.roles.destroy.replace(/0$/, id)
    }
  },
  mixins: [ContentTableMixin],
  beforeMount() {
    this.url = this.$attrs.routes.roles.list
    this.getCollection()
  }
}
</script>
