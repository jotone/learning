<template>
  <div class="header">
    <a class="logo" :href="$page.props.routes.dashboard.index" @click.prevent="showSideMenu"></a>

    <div class="header-optionals">
      <slot name="optionals"/>
    </div>
  </div>

  <div class="content-wrap">
    <SideMenu/>

    <div class="content">
      <slot name="content"/>
    </div>
  </div>

  <div class="notifications-wrap"><ul></ul></div>

  <div class="preloader">
    <div class="preloader-spinner">
      <img src="/images/preloader.svg" alt="Loading. Please, wait for a while&hellip;">
    </div>
  </div>
</template>

<script>

import SideMenu from './SideMenu.vue';

export default {
  components: {SideMenu},
  name: "DefaultLayout",
  methods: {
    showSideMenu(e) {
      const _this = $(e.target).closest('a')
      if ($(window).width() >= 480) {
        window.location = _this.attr('href')
      } else {
        $('.side-menu').toggleClass('active')
      }
    }
  }
}
</script>