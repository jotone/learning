<template>
  <div class="header">
    <a class="logo" :href="attrs.routes.dashboard.index"></a>
  </div>

  <div class="content-wrap">

    <SideMenu :menu="attrs.menu"/>

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
  props: ["attrs"]
}
</script>

<style scoped>

</style>