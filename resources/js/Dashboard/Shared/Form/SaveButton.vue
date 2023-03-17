<template>
  <button type="submit" :form="form || null" class="btn">
    <i class="icon save-icon"></i>
  </button>
</template>

<script>
export default {
  name: "SaveButton",
  props: ["form"]
}
</script>