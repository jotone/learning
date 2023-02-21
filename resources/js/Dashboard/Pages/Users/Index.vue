<template>
  <DefaultLayout :menu="$attrs.menu" :routes="$attrs.routes">
    <TopMenu :menu="$attrs.top_menu"/>
  </DefaultLayout>
</template>

<script>

import DefaultLayout from "../../Layouts/DefaultLayout.vue";
import TopMenu from "../../Layouts/TopMenu.vue";

export default {
  components: {DefaultLayout, TopMenu},
  name: "UsersIndex.vue"
}
</script>
