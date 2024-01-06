<template>
  <Layout>
    <slot></slot>
  </Layout>
</template>

<script setup>
import axios from "axios";
import {provide} from "vue";
import {usePage} from "@inertiajs/vue3";
import Layout from "./Layout.vue";

defineOptions({layout: Layout})

const page = usePage()

/**
 * GraphQL Query request
 * @returns {Promise<axios.AxiosResponse<any>>}
 */
const request = (url, query) => new Promise((resolve, reject) => {
  axios.post(url, {'query': query}, {
    headers: {
      accept: "application/json",
      Authorization: "Bearer " + page.props.auth.apiToken
    }
  }).then(response => {
    if (200 === response.status) {
      if ('errors' in response.data) {
        console.error(response.data.errors)
        reject(response.data.errors)
      } else {
        resolve(response)
      }
    }
  })
})

provide('request', request)
</script>