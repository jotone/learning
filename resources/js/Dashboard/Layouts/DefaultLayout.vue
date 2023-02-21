<template>
  <div class="header">
    <a class="logo" :href="routes.dashboard.index"></a>
  </div>

  <div class="content-wrap">

    <SideMenu :menu="menu"/>

    <div class="content">
      <slot />
    </div>
  </div>
</template>

<script>

import SideMenu from './SideMenu.vue';

export default {
  components: {SideMenu},
  name: "DefaultLayout",
  props: ["menu", "routes"]
}
</script>