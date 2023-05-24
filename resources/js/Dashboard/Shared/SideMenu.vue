<template>
  <nav class="side-menu">
    <ul class="side-menu-top">
      <template v-for="item in $page.props.menu">
        <SideMenuItem :item="item" :view="item.is_top"/>
      </template>
    </ul>
    <ul class="side-menu-bottom">
      <template v-for="item in $page.props.menu">
        <SideMenuItem :item="item" :view="!item.is_top"/>
      </template>
      <li class="user-link">
        <Avatar/>
        <ul>
          <li class="user-link-info-wrap">
            <div class="user-link-info-image">
              <Avatar/>
            </div>
            <div class="user-link-info">
              <div class="user-link-info-name">
                {{ $page.props.auth.first_name }} {{ $page.props.auth.last_name }}
              </div>
              <div class="user-link-info-email">
                {{ $page.props.auth.email }}
              </div>
            </div>
          </li>
          <li>
            <a :href="$page.props.routes.auth.me">{{ __('menu.profile') }}</a>
          </li>
          <li>
            <a :href="$page.props.routes.auth.logout">{{ __('menu.logout') }}</a>
          </li>
        </ul>
      </li>
    </ul>

    <div class="close" @click="closeSideMenu"></div>
  </nav>
</template>

<script>

import Avatar from "../Components/User/Avatar.vue";
import SideMenuItem from "./SideMenuItem.vue";

export default {
  components: {Avatar, SideMenuItem},
  name: "SideMenu",
  methods: {
    closeSideMenu(e) {
      $(e.target).closest('.side-menu').removeClass('active')
    }
  }
}
</script>