<template>
  <ul class="breadcrumbs">
    <li>
      <span>Roles</span>
    </li>
  </ul>

  <header>
    <div class="page-name-wrap">
      <h1>Roles</h1>

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
          <template v-for="(role, i) in list.data">
            <TableRow :role="role"/>
          </template>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { usePage } from "@inertiajs/vue3";
import { ref } from "vue";

import DataTableLayout from "../../../shared/DataTableLayout.vue";
import SearchForm from "../../../components/DataTables/SearchForm.vue";
import TableRow from "./TableRow.vue";

defineOptions({layout: DataTableLayout})

let list = ref([]);
const page = usePage()

axios.post(
  page.props.routes.roles.api,
  {
    'query': '{roles(per_page:25,order_by:"name",order_dir:"asc") {total per_page last_page has_more_pages current_page data {id name level created_at}}}'
  },
  {
    headers: {
      accept: "application/json",
      Authorization: "Bearer " + page.props.auth.apiToken
    }
  }
).then(response => {
  console.log(response.data)
  200 === response.status && (list.value = response.data.data.roles)
})
</script>