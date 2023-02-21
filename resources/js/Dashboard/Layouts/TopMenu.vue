<template>
  <nav class="content-submenu">
    <ul>
      <li v-for="item in menu" :class="{'active': isActive(item.route)}">
        <Link :href="item.route">
          <span>{{ item.name }}</span>
        </Link>
      </li>
    </ul>
  </nav>
</template>

<script>

import {Link} from "@inertiajs/vue3";

export default {
  components: {Link},
  name: "TopMenu",
  props: ['menu'],
  methods: {
    /**
     * Check the current menu item should be active
     * @param {string} route
     * @returns {boolean}
     */
    isActive(route) {
      return window.location.pathname === route;
    }
  }
}
</script>