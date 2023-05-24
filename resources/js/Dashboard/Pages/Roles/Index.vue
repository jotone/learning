<template>
  <Layout>
    <template v-slot:optionals>
      <a class="btn" :href="$attrs.routes.roles.create">
        <i class="icon plus-icon"></i>
        <span>{{ __('role.create') }}</span>
      </a>
    </template>

    <template v-slot:content>
      <div class="page-content-wrap">
        <Pagination :options="pagination" :url="url"/>

        <div class="table-group">

          <div class="table-optional">
            <div class="table-info"></div>

            <div class="table-filtering">
              <SearchForm :placeholder="__('role.search')"/>
            </div>
          </div>

          <div class="table-wrap">
            <table>
              <thead>
              <tr>
                <ContentTableHead field="name" :name="__('common.name')"/>
                <ContentTableHead field="level" :name="__('role.fields.level')"/>
                <ContentTableHead field="created_at" :name="__('common.created_at')"/>
                <th>
                  <span>{{ __('common.actions') }}</span>
                </th>
              </tr>
              </thead>
              <tbody>
              <template v-for="(n, i) in collection.length">
                <RoleTableRow :model="collection[i]"/>
              </template>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <Confirmation
        okBtnClass="danger"
        ref="confirmation"
        :okText="__('common.remove')"
        :text="__('role.msg.ask_remove', removalName)"
      />
    </template>
  </Layout>
</template>

<script>
import {ContentTableMixin} from "../../Mixins/content-table-mixin";
import RoleTableRow from "./Partials/RoleTableRow.vue";

export default {
  name: "Roles/Index",
  components: {RoleTableRow},
  mixins: [ContentTableMixin],
  beforeMount() {
    this.url = this.$attrs.routes.roles.list
  }
}
</script>