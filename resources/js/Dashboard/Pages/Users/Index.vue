<template>
  <Layout>
    <template v-slot:optionals>
      <a class="btn" :href="$attrs.routes.users.create">
        <i class="icon user-add-icon"></i>
        <span>{{ __('user.create') }}</span>
      </a>
    </template>

    <template v-slot:content>
      <div class="page-content-wrap">
        <Pagination :options="pagination" :url="url"/>

        <div class="table-group">

          <div class="table-optional">
            <div class="table-info"></div>

            <div class="table-filtering">
              <SearchForm :placeholder="__('user.search')"/>
            </div>
          </div>

          <div class="table-wrap">
            <table>
              <thead>
              <tr>
                <ContentTableHead field="first_name" :name="__('user.fields.first_name')"/>
                <ContentTableHead field="last_name" :name="__('user.fields.last_name')"/>
                <ContentTableHead field="email" :name="__('user.fields.email')"/>
                <ContentTableHead field="role_name" :name="__('role.single')"/>
                <th><span>{{ __('common.image.single') }}</span></th>
                <ContentTableHead field="created_at" :name="__('common.created_at')"/>
                <th>
                  <span>{{ __('common.actions') }}</span>
                </th>
              </tr>
              </thead>
              <tbody>
              <template v-for="(model, i) in collection">
                <UserTableRow :model="collection[i]"/>
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
        :text="__('user.msg.ask_remove', removalName)"
      />
    </template>
  </Layout>
</template>

<script>
import {ContentTableMixin} from "../../Mixins/content-table-mixin";
import axios from "axios";
import UserTableRow from "./Partials/UserTableRow.vue";

export default {
  components: {UserTableRow},
  mixins: [ContentTableMixin],
  name: "Users/Index",
  beforeMount() {
    this.url = this.$attrs.routes.users.list
  }
}
</script>