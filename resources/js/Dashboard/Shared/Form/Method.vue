<template>
  <input name="_method" type="hidden" :value="value">
</template>

<script>
export default {
  name: "Method",
  props: {
    value: {
      type: String,
      required: true
    }
  }
}
</script>