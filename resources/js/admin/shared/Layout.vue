<template>
  <div class="site-content-wrap">
    <nav>
      <a class="logo-image" :href="$page.props.routes.dashboard.index">
        <img :src="$page.props.settings.logo_img_admin" alt=""/>
      </a>

      <ul class="admin-menu" :class="{active: sideMenuActive}">
        <li v-for="block in $page.props.menu">
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
              <Avatar :user="$page.props.auth"/>

              <div class="user-credentials">
                <div class="user-name">{{ $page.props.auth.first_name }} {{ $page.props.auth.last_name }}</div>
                <div class="user-email">{{ $page.props.auth.email }}</div>
              </div>
            </li>
            <li>
              <a href="#">
                <i class="icon profile-icon"></i>
                <span>Edit Admin Profile</span>
              </a>
            </li>
            <li>
              <a :href="$page.props.routes.auth.logout">
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
        <li v-for="item in $page.props.breadcrumbs">
          <a :href="item.url" v-if="'url' in item">
            {{ item.name }}
          </a>
          <span v-else>{{ item.name }}</span>
        </li>
      </ul>
      <slot></slot>
    </main>
  </div>
</template>

<script setup>
import moment from "moment";
import { ref, provide } from "vue";
import Avatar from "../components/User/Avatar.vue";
import SideMenuItem from "../components/SideMenu/SideMenuItem.vue";

/**
 * Toggle the active status for the side menu
 */
let sideMenuActive = ref(false);
const viewSideMenu = () => {
  sideMenuActive.value = !sideMenuActive.value
}

/**
 * Convert unix date (Y-m-d H:i:s) to the proper date format
 *
 * @param {string} date
 * @param {string} format
 * @returns {string}
 */
const convertDate = (date, format = 'DD MMM YYYY') => moment(date).format(format)

// Provides the "convertDate" function on over the all project
provide('convertDate', convertDate)
</script>
