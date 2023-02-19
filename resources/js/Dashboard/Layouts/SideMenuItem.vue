<template>
  <li v-if="view" :class="{'active': isActive}">
    <Link :href="item.route" v-if="!isExternal">
      <i v-if="!!item.img" v-html="item.img"></i>
      <span>{{ item.name }}</span>
    </Link>

    <a :href="item.route" target="_blank" v-else>
      <i v-if="!!item.img" v-html="item.img"></i>
      <span>{{ item.name }}</span>
    </a>
  </li>
</template>

<script>
import {Link} from "@inertiajs/vue3";

export default {
  components: {Link},
  name: "SideMenuItem",
  props: ["item", "view"],
  computed: {
    /**
     * Check the current side menu item should be active
     * @returns {boolean}
     */
    isActive() {
      return window.location.pathname.startsWith(this.item.route)
        && (this.item.route !== '/dashboard' || window.location.pathname === '/dashboard')
    },
    /**
     * Check if link is external
     * @returns {boolean}
     */
    isExternal() {
      return this.item.route.startsWith('http')
    }
  }
}
</script>