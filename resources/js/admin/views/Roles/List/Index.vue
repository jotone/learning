<template>
  <header>
    <div class="page-name-wrap">
      <h1>{{ page.props.pageName }}</h1>

      <a class="btn">
        <i class="icon plus-icon"></i>
        <span>Role Create</span>
      </a>
    </div>
  </header>

  <div class="card">
    <div class="content-table-wrap">
      <div class="content-table-controls">
        <SearchForm
          placeholder="Search for a Role by the name or slug…"
          :search="$page.props.filters?.search"
        />
      </div>

      <div class="table-container">
        <table>
          <thead>
          <tr>
            <th class="static">
              <div class="static-fields">
                <div class="col-name">
                  <span>Name</span>
                  <div class="order"></div>
                </div>
              </div>
            </th>
            <th>
              <div class="col-name">
                <span>Level</span>
                <div class="order"></div>
              </div>
            </th>
            <th>
              <div class="col-name">
                <span>Creation Date</span>
                <div class="order"></div>
              </div>
            </th>
            <th>
              <div class="col-name">
                <span>Actions</span>
              </div>
            </th>
          </tr>
          </thead>
          <tbody>
          <template v-for="role in list.data">
            <TableRow :role="role"/>
          </template>
          </tbody>
        </table>
      </div>

      <div class="page-controls-wrap">
        <PerPage/>
        <Pagination
          v-if="list.has_more_pages ?? false"
          :current="list.current_page"
          :filters="filters"
          :last="list.last_page"
          :perPage="list.per_page"
          :total="list.total"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import {usePage} from "@inertiajs/vue3";
import {inject, ref} from "vue";
import {decodeUriQuery} from "../../../libs/RequestHelper"

import DataTableLayout from "../../../shared/DataTableLayout.vue";
import TableRow from "./TableRow.vue";
import {Pagination, PerPage, SearchForm} from '../../../components/DataTables';

defineOptions({layout: DataTableLayout})
// Get content roles function
const getList = inject('getList')

const page = usePage()

let list = ref([]);

const {origin: siteUrl, pathname: path, search} = window.location
const query = decodeUriQuery(search)

let filters = {
  page: query.page || 1,
  per_page: query.per_page ?? 2,
  order: {
    by: query.order?.by ?? 'created_at',
    dir: query.order?.dir ?? 'desc'
  },
  search: query.search ?? ''
}

/**
 * GraphQL query string to get roles list
 *
 * @param filters
 * @returns {string}
 */
const buildQuery = filters => `{
  roles(
    per_page:${filters.per_page},
    order_by:"${filters.order.by}",
    order_dir:"${filters.order.dir}",
    page:${filters.page},
    search:"${filters.search}"
  ) {
    total per_page last_page has_more_pages current_page data {
      id name level created_at
    }
  }
}`;

getList(page.props.routes.roles.api, buildQuery(filters)).then(response => 200 === response.status && (list.value = response.data.data.roles))

</script>