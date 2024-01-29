<template>
  <div class="site-content-wrap">
    <nav>
      <a class="logo-image" :href="$attrs.routes.dashboard.index">
        <img :src="$attrs.settings.logo_img_admin" alt=""/>
      </a>

      <ul class="admin-menu" :class="{active: sideMenuActive}">
        <li v-for="block in $attrs.menu">
          <ul>
            <template v-for="item in block">
              <SideMenuItem :item="item"/>
            </template>
          </ul>
        </li>

        <li class="account-type-wrap">
          <p>You are now on the Basic Plan. Upgrade to PRO for more resources.</p>

          <a href="#">Upgrade</a>
        </li>

        <li class="actor-menu-wrap">
          <ul>
            <li class="user-info-wrap">
              <Avatar :user="$attrs.auth"/>

              <div class="user-credentials">
                <div class="user-name">{{ $attrs.auth.first_name }} {{ $attrs.auth.last_name }}</div>
                <div class="user-email">{{ $attrs.auth.email }}</div>
              </div>
            </li>
            <li>
              <a href="#">
                <i class="icon profile-icon"></i>
                <span>Edit Admin Profile</span>
              </a>
            </li>
            <li>
              <a :href="$attrs.routes.auth.logout">
                <i class="icon logout-icon"></i>
                <span>Log Out</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>

      <div class="view-menu-button" @click="viewSideMenu">
        <i class="icon burger-menu-icon"></i>
      </div>
    </nav>
    <main>
      <ul class="breadcrumbs" v-if="$attrs.hasOwnProperty('breadcrumbs')">
        <li v-for="item in $attrs.breadcrumbs">
          <a :href="item.url" v-if="'url' in item">
            {{ item.name }}
          </a>
          <span v-else>{{ item.name }}</span>
        </li>
      </ul>
      <slot/>
    </main>
  </div>
</template>

<script setup>
import {ref, provide} from "vue";
import {usePage} from "@inertiajs/vue3";
import {Notification} from "../libs/Notification.js";
import axios from "axios";
import moment from "moment";

import Avatar from "../components/User/Avatar.vue";
import SideMenuItem from "../components/SideMenu/SideMenuItem.vue";

const page = usePage();

console.log(page.props.auth.apiToken)

/**
 * Toggle the active status for the side menu
 */
let sideMenuActive = ref(false);
const viewSideMenu = () => {
  sideMenuActive.value = !sideMenuActive.value
}

/*
 * Methods
 */
/**
 * Convert unix date (Y-m-d H:i:s) to the proper date format
 * @param {string} date
 * @param {string} format
 * @returns {string}
 */
const convertDate = (date, format = 'DD MMM YYYY') => moment(date).format(format)
/**
 * Make HTTP requests using Axios
 * @param {object} props
 * @return {Promise}
 */
const request = props => {
  // Check if the 'url' property is provided in the props, throw an error if not.
  if (!props.hasOwnProperty('url')) {
    throw new ReferenceError("Url action attribute is not declared.");
  }
  // Set the HTTP method to 'get' by default if it's not specified in the props.
  props.method = !props.hasOwnProperty("method") ? "get" : props.method.toLowerCase();
  // Set default headers for the request.
  props.headers = Object.assign({
    accept: "application/json",
    authorization: "Bearer " + page.props.auth.apiToken,
    "Content-Type": props.method === "get" || props.method === "post"
      ? "multipart/form-data"
      : "application/x-www-form-urlencoded"
  }, props.headers || {});
  // Return a new Promise that resolves when the Axios request is successful.
  return new Promise(resolve => axios(props)
    .then(response => {
      // Reload page by condition
      if ('forceReload' in props && props.forceReload) {
        window.location.reload();
      }
      // Run onSuccess callback if exists
      if ("onSuccess" in props) {
        typeof props.onSuccess === "function" && props.onSuccess(response);
      }

      resolve(response);
    })
    .catch(e => console.error(e))
  )
}
/**
 * GraphQL request
 * @returns {Promise<axios.AxiosResponse<any>>}
 */
const requestGraphQL = (url, query) => new Promise((resolve, reject) => {
  axios.post(url, {'query': query}, {
    headers: {
      accept: "application/json",
      Authorization: "Bearer " + page.props.auth.apiToken
    }
  }).then(response => {
    if (200 === response.status) {
      if ('errors' in response.data) {
        console.error(response.data.errors)
        for (let i = 0, n = response.data.errors.length; i < n; i++) {
          const error = response.data.errors[i];
          Notification.danger(error.message);
        }
        reject(response.data.errors)
      } else {
        resolve(response)
      }
    }
  })
})

// Provide the "convertDate", "request", "requestGraphQL" functions on over the all projects
provide('request', request)
provide('requestGraphQL', requestGraphQL)
provide('convertDate', convertDate)
</script>
