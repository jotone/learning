<template>
  <nav class="content-submenu">
    <ul>
      <li
        v-for="item in $page.props.topMenu"
        :class="{'active': isActive(item.route)}"
        :data-name="item.name.toLowerCase().replace(/\s/, '-')"
      >
        <Link :href="item.route">
          <span>{{ __('menu.' + item.name) }}</span>
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
  methods: {
    /**
     * Check the current menu item should be active
     * @param {string} route
     * @returns {boolean}
     */
    isActive(route) {
      return window.location.pathname.replace(/(\/create|\/me|\/edit\/\d+)/, "") === route;
    }
  }
}
</script>