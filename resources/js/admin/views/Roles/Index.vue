<template>
  <ul class="breadcrumbs">
    <li>
      <span>Roles</span>
    </li>
  </ul>

  <div class="page-name-wrap">
    <h1>Roles</h1>

    <a class="btn">
      <i class="icon plus-icon"></i>
      <span>Role Create</span>
    </a>
  </div>
</template>

<script setup>
import axios from "axios";

import DataTableLayout from "../../shared/DataTableLayout.vue";
import {usePage} from "@inertiajs/vue3";
import {reactive} from "vue";

defineOptions({layout: DataTableLayout})

let list = reactive([]);
const page = usePage()

axios.post(
  page.props.routes.roles,
  {
    'query': '{roles(per_page:25,order_by:"name",order_dir:"asc") {total per_page last_page has_more_pages current_page data {id name slug level}}}'
  },
  {
    headers: {
      accept: "application/json",
      Authorization: "Bearer " + page.props.auth.apiToken
    }
  }
).then(response => {
  200 === response.status && (list = response.data.data.roles)

  console.log(list)
})
</script>