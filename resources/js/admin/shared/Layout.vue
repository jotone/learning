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
            <UserInfo :user="$attrs.auth" tag="li"/>
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
      <ul class="breadcrumbs content-container" v-if="$attrs.hasOwnProperty('breadcrumbs')">
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
// Vue libs
import {ref, provide} from "vue";
import {usePage} from "@inertiajs/vue3";
// Other Libs
import axios from "axios";
import moment from "moment";
import {Notification} from "../libs/Notification.js";
// Components
import SideMenuItem from "../components/Menu/SideMenuItem.vue";
import UserInfo from '../components/User/Info.vue';

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
const convertDate = (date, format = 'DD MMM YYYY') => moment(date).format(format);
/**
 * Get element index related to the parent node
 * @param el
 * @returns {number|null}
 */
// const index = el => Array.from(el.parentNode.querySelectorAll(el.nodeName.toLowerCase())).indexOf(el);
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
  return new Promise((resolve, reject) => axios(props)
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
    .catch(e => reject(e))
  )
}
/**
 * GraphQL request
 * @returns {Promise<axios.AxiosResponse<any>>}
 */
const requestGraphQL = (url, query, headers = {}) => new Promise((resolve, reject) => axios
  .post(url, typeof query === 'string' ? {'query': query} : query, {
    headers: Object.assign({
      accept: "application/json",
      Authorization: "Bearer " + page.props.auth.apiToken
    }, headers)
  }).then(response => {
    if (200 === response.status) {
      if ('errors' in response.data) {
        for (let i = 0, n = response.data.errors.length; i < n; i++) {
          const error = response.data.errors[i];
          if (error.extensions.hasOwnProperty('validation')) {
            for (let field in error.extensions.validation) {
              const errorMessages = error.extensions.validation[field]
              for (let j = 0, m = errorMessages.length; j < m; j++) {
                Notification.danger(errorMessages[j]);
              }
            }
          } else {
            Notification.danger(response.data.errors[i].message);
          }
        }
        reject(response.data.errors)
      } else {
        resolve(response)
      }
    }
  }))

/**
 * Build a query for the GraphQL request to upload a file
 * @param {File} file
 * @param {int} id
 * @param {string} type
 * @param {string} field
 * @returns {Object}
 */
const graphQlFileUploadQuery = (file, id, type, field) => ({
    operations: JSON.stringify({
      query: `mutation ${type}($id: Int!, $file: Upload!) {update(id: $id, ${field}: $file) {id ${field}}}`,
      variables: {id: id, file: null}
    }),
    map: JSON.stringify({'0': ['variables.file']}),
    '0': file
})

/**
 * GraphQL form serialization function
 * @param {string} mutationType
 * @param {Object} form
 * @param {string, Array} responseField
 * @param {Array} except
 * @returns {string}
 */
const graphQlSerializeForm = (mutationType, form, responseField, except = []) => {
  let query = []
  for (let field in form) {
    const value = form[field];
    if (except.indexOf(field) < 0) {
      if (Array.isArray(value)) {
        query.push(`${field}:[${value.join(',')}]`);
      } else if (isNumber(value)) {
        query.push(`${field}:${value}`);
      } else if (typeof value === 'string') {
        query.push(`${field}:"${value}"`);
      } else if (typeof value === 'boolean') {
        query.push(`${field}:${value ? 1 : 0}`);
      } else if (null === value) {
        query.push(`${field}:""`)
      } else {
        console.error('Unknown query field type', field, value)
      }
    }
  }

  if (Array.isArray(responseField)) {
    responseField = responseField.join(',')
  }

  return `mutation {${mutationType} (${query.join(',')}) { ${responseField} }}`
}

const isNumber = n => !isNaN(parseFloat(n)) && isFinite(n);


// Provide the functions on over the all projects
provide('convertDate', convertDate);
provide('graphQlFileUploadQuery', graphQlFileUploadQuery);
provide('graphQlSerializeForm', graphQlSerializeForm);
provide('request', request);
provide('requestGraphQL', requestGraphQL);
</script>
