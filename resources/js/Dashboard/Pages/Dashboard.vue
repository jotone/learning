<template>
  <DefaultLayout :menu="$attrs.menu" :routes="$attrs.routes">

  </DefaultLayout>
</template>

<script>

import DefaultLayout from "../Layouts/DefaultLayout.vue";

export default {
  components: {DefaultLayout},
  name: "Dashboard.vue"
}
</script>
