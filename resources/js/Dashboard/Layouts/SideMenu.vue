<template>
  <nav class="side-menu">
    <ul class="side-menu-top">
      <template v-for="item in menu">
        <SideMenuItem :item="item" :view="item.is_top"/>
      </template>
    </ul>
    <ul class="side-menu-bottom">
      <template v-for="item in menu">
        <SideMenuItem :item="item" :view="!item.is_top"/>
      </template>
    </ul>

    <div class="close" @click="closeSideMenu"></div>
  </nav>
</template>

<script>

import SideMenuItem from "./SideMenuItem.vue";

export default {
  components: {SideMenuItem},
  name: "SideMenu",
  props: ["menu"],
  methods: {
    closeSideMenu(e) {
      $(e.target).closest('.side-menu').removeClass('active')
    }
  }
}
</script>