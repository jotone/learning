<template>
  <Layout>
    <template v-slot:optionals>
      <a class="btn" :href="$attrs.routes.coaches.create">
        <i class="icon plus-icon"></i>
        <span>{{ __('coach.create') }}</span>
      </a>
    </template>

    <template v-slot:content>
      <div class="page-content-wrap">
        <Pagination :options="pagination" :url="url"/>

        <div class="table-group">

          <div class="table-optional">
            <div class="table-info"></div>

            <div class="table-filtering">
              <SearchForm :placeholder="__('coach.search')"/>
            </div>
          </div>

          <div class="table-wrap">
            <table>
              <thead>
              <tr>
                <ContentTableHead field="first_name" :name="__('user.fields.first_name')"/>
                <ContentTableHead field="last_name" :name="__('user.fields.last_name')"/>
                <ContentTableHead field="email" :name="__('user.fields.email')"/>
                <ContentTableHead field="created_at" :name="__('common.created_at')"/>
                <th>
                  <span>{{ __('common.actions') }}</span>
                </th>
              </tr>
              </thead>
              <tbody :data-i="collection.length" :data-c="JSON">
              <template v-for="(n, i) in collection.length">
                <CoachTableRow :model="collection[i]"/>
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
        :text="__('coach.msg.ask_remove', removalName)"
      />
    </template>
  </Layout>
</template>

<script>
import {ContentTableMixin} from "../../Mixins/content-table-mixin";
import CoachTableRow from "./Partials/CoachTableRow.vue";

export default {
  name: "Coaches/Index",
  components: {CoachTableRow},
  mixins: [ContentTableMixin],
  beforeMount() {
    this.url = this.$attrs.routes.coaches.list
  }
}
</script>