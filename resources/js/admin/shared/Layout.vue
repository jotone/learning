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
      <ul class="breadcrumbs">
        <li v-for="item in $attrs.breadcrumbs">
          <a :href="item.url" v-if="'url' in item">
            {{ item.name }}
          </a>
          <span v-else>{{ item.name }}</span>
        </li>
      </ul>

      <header>
        <div class="page-name-wrap">
          <h1>{{ $attrs.pageName }}</h1>

          <template v-if="'buttons' in $attrs">
            <a v-for="button in $attrs.buttons" class="btn" :href="button.url">
              <i v-if="button.hasOwnProperty('icon')" class="icon" :class="button.icon"></i>
              <span>{{ button.name }}</span>
            </a>
          </template>
        </div>
      </header>

      <TransitionGroup class="notification-list" name="notification-list" tag="ul" v-if="notificationList.length">
        <li
          v-for="(message, i) in notificationList"
          v-html="message.text"
          :class="message.type"
          :key="i"
        ></li>
      </TransitionGroup>

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

/**
 * Toggle the active status for the side menu
 */
let sideMenuActive = ref(false);
const viewSideMenu = () => {
  sideMenuActive.value = !sideMenuActive.value
}

// Listen to the custom event (if you choose to use event dispatching)
let notificationList = ref([]);
// Notification delay time, ms
const notificationDelay = 3000
// Listen if the localStorage.setItem function was called
window.addEventListener('localStorageSetItem', e => {
  if ('notifications' === e.detail.key && !!e.detail.value) {
    Notification.get().then(notifications => {
      // View notifications
      notificationList.value = notifications
      // Remove notifications
      setTimeout(() => {
        notificationList.value = []
      }, notificationDelay)
    })
  }
});

/**
 * Convert unix date (Y-m-d H:i:s) to the proper date format
 * @param {string} date
 * @param {string} format
 * @returns {string}
 */
const convertDate = (date, format = 'DD MMM YYYY') => moment(date).format(format)

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

provide('request', request)
// Provides the "convertDate" function on over the all project
provide('convertDate', convertDate)
</script>
