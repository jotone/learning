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
 *
 * @returns {Promise<axios.AxiosResponse<any>>}
 */
const getList = (url, query) => axios.post(url, {'query': query}, {
  headers: {
    accept: "application/json",
    Authorization: "Bearer " + page.props.auth.apiToken
  }
});

provide('getList', getList)
</script>